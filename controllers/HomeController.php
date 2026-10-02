<?php

namespace Controllers;

use Nucleo\Controller;
use Modelos\Vacina;

/**
 * Controlador da pagina inicial.
 */
class HomeController extends Controller
{
        public function index(): void
    {
        $vacinasVencidas = [];
        $vacinasProximas = [];
        $indicadoresMes  = [
            'atendimentos_mes' => 0,
            'faturamento_mes'  => 0.0,
            'total_animais'    => 0,
            'vacinas_alerta'   => 0,
        ];

        // RF15 e Desafio Bônus: Apenas para a equipe logada
        if (autenticado()) {
            $modeloVacina = new Vacina();
            $hoje = date('Y-m-d');
            $trintaDias = date('Y-m-d', strtotime('+30 days'));

            // Vacinas com data de retorno vencida
            $sqlVencidas = "SELECT v.*, a.nome AS animal_nome 
                            FROM vacinas v 
                            LEFT JOIN animais a ON a.id = v.animal_id 
                            WHERE v.data_retorno IS NOT NULL 
                              AND v.data_retorno != '' 
                              AND v.data_retorno < ? 
                            ORDER BY v.data_retorno ASC";

            // Vacinas que vencem nos proximos 30 dias
            $sqlProximas = "SELECT v.*, a.nome AS animal_nome 
                            FROM vacinas v 
                            LEFT JOIN animais a ON a.id = v.animal_id 
                            WHERE v.data_retorno >= ? 
                              AND v.data_retorno <= ? 
                            ORDER BY v.data_retorno ASC";

            $vacinasVencidas = $modeloVacina->consultar($sqlVencidas, [$hoje]);
            $vacinasProximas = $modeloVacina->consultar($sqlProximas, [$hoje, $trintaDias]);

            // Bônus 1: Indicadores do mês atual
            $sqlAtendimentos = "SELECT COUNT(*) AS total, COALESCE(SUM(valor_cobrado), 0) AS faturamento 
                                FROM atendimentos 
                                WHERE situacao = 'realizado' 
                                  AND MONTH(data_hora) = MONTH(CURRENT_DATE()) 
                                  AND YEAR(data_hora) = YEAR(CURRENT_DATE())";
            $resAtendimentos = $modeloVacina->consultar($sqlAtendimentos);

            $sqlAnimais = "SELECT COUNT(*) AS total FROM animais";
            $resAnimais = $modeloVacina->consultar($sqlAnimais);

            $indicadoresMes = [
                'atendimentos_mes' => (int) ($resAtendimentos[0]['total'] ?? 0),
                'faturamento_mes'  => (float) ($resAtendimentos[0]['faturamento'] ?? 0),
                'total_animais'    => (int) ($resAnimais[0]['total'] ?? 0),
                'vacinas_alerta'   => count($vacinasVencidas) + count($vacinasProximas),
            ];
        }

        $this->view('home/index', [
            'titulo'          => 'Início',
            'vacinasVencidas' => $vacinasVencidas,
            'vacinasProximas' => $vacinasProximas,
            'indicadoresMes'  => $indicadoresMes,
        ]);
    }

    public function sobre(): void
    {
        $this->view('home/sobre', [
            'titulo' => 'Sobre a Clínica Pata Amiga',
        ]);
    }
}