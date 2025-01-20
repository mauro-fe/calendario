<?php

namespace App\Helpers;

use GUMP;

class Validator
{
    private $errors = [];

    public function validate($data, $rules)
    {
        $gump = new GUMP();

        // Adicionar validador customizado
        $gump->add_validator('after_or_equal', function ($field, $input, $param = null) {
            if (!is_string($param) || !isset($input[$param])) {
                return false; // Parâmetro inválido ou não existente
            }

            $startDate = strtotime($input[$param]); // Data inicial
            $endDate = strtotime($input[$field]);  // Data final

            // Adicione um log para depurar os valores
            error_log("Start Date: " . $input[$param]);
            error_log("End Date: " . $input[$field]);

            return $endDate >= $startDate; // Verifica se a data final é maior ou igual
        }, 'A data final deve ser igual ou posterior à data inicial.');


        // Definir as regras de validação
        $gump->validation_rules($rules);

        // Validar os dados
        $validated_data = $gump->run($data);

        if ($validated_data === false) {
            $this->errors = $gump->get_errors_array(); // Armazena os erros
            return ['valid' => false, 'errors' => $this->errors];
        }

        return ['valid' => true, 'data' => $validated_data];
    }

    public function getErrors()
    {
        return $this->errors; // Retorna os erros armazenados
    }
}
