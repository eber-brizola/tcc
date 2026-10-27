<?php

/**
 * Reserva uma tentativa antes da operação administrativa.
 * Chamar antes de iniciar a transação que altera os operadores.
 */
function reservarConfirmacaoRoot(mysqli $conexao, int $idAutor): bool
{
    if ($idAutor < 1) {
        throw new InvalidArgumentException('Operador inválido.');
    }

    $transacaoAberta = false;

    try {
        mysqli_begin_transaction($conexao);
        $transacaoAberta = true;

        // Cria o contador se necessário.
        // A chave primária impede registros duplicados.
        $stmt = mysqli_prepare(
            $conexao,
            'INSERT INTO root_confirmacoes
                (id_operador, tentativas, inicio_janela)
             VALUES (?, 0, NOW())
             ON DUPLICATE KEY UPDATE
                id_operador = root_confirmacoes.id_operador'
        );

        mysqli_stmt_bind_param($stmt, 'i', $idAutor);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Serializa as tentativas do mesmo root, mesmo em sessões diferentes.
        $stmt = mysqli_prepare(
            $conexao,
            'SELECT tentativas,
                    (inicio_janela <= NOW() - INTERVAL 15 MINUTE)
                        AS expirou
             FROM root_confirmacoes
             WHERE id_operador = ?
             FOR UPDATE'
        );

        mysqli_stmt_bind_param($stmt, 'i', $idAutor);
        mysqli_stmt_execute($stmt);

        $controle = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$controle) {
            throw new RuntimeException(
                'Não foi possível consultar o limite de confirmações.'
            );
        }

        $expirou = (int) $controle['expirou'] === 1;

        if (!$expirou && (int) $controle['tentativas'] >= 5) {
            mysqli_commit($conexao);
            $transacaoAberta = false;

            return false;
        }

        if ($expirou) {
            $sql = 'UPDATE root_confirmacoes
                    SET tentativas = 1, inicio_janela = NOW()
                    WHERE id_operador = ?';
        } else {
            $sql = 'UPDATE root_confirmacoes
                    SET tentativas = tentativas + 1
                    WHERE id_operador = ?';
        }

        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $idAutor);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Persiste a tentativa independentemente da ação seguinte.
        mysqli_commit($conexao);
        $transacaoAberta = false;

        return true;

    } catch (Throwable $erro) {
        if ($transacaoAberta) {
            try {
                mysqli_rollback($conexao);
            } catch (Throwable $erroRollback) {
                error_log($erroRollback->getMessage());
            }
        }

        // Se não foi possível registrar a tentativa, a ação não prossegue.
        throw $erro;
    }
}