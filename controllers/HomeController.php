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

        // RF15: Apenas para a equipe logada, busca os retornos vencidos e a vencer
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
        }

        $this->view('home/index', [
            'titulo'          => 'Início',
            'vacinasVencidas' => $vacinasVencidas,
            'vacinasProximas' => $vacinasProximas,
        ]);
    }

    public function sobre(): void
    {
        $this->view('home/sobre', [
            'titulo' => 'Sobre a Clínica Pata Amiga',
        ]);
    }
}