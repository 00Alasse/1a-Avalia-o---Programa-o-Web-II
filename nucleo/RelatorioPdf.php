<?php

namespace Nucleo;

use RuntimeException;

final class RelatorioPdf
{
    private const LARGURA_PAGINA = 595.0;
    private const MARGEM = 36.0;
    private const ALTURA_LINHA = 18.0;
    private const ALTURA_CABECALHO = 22.0;
    private const LINHAS_POR_PAGINA = 38;

    public static function gerar(string $titulo, array $colunas, array $linhas, string $arquivo): void
    {
        $pdf = self::conteudo($titulo, $colunas, $linhas);

        $diretorio = dirname($arquivo);

        if (
            !is_dir($diretorio)
            && !mkdir($diretorio, 0777, true)
            && !is_dir($diretorio)
        ) {
            throw new RuntimeException(
                "Nao foi possivel criar a pasta do relatorio: {$diretorio}"
            );
        }

        if (file_put_contents($arquivo, $pdf, LOCK_EX) === false) {
            throw new RuntimeException(
                "Nao foi possivel gravar o relatorio: {$arquivo}"
            );
        }
    }

    public static function conteudo(
        string $titulo,
        array $colunas,
        array $linhas
    ): string {
        $colunas = array_values(
            array_map(
                static fn($coluna): string => (string) $coluna,
                $colunas
            )
        );

        if ($colunas === []) {
            $colunas = ['id'];
        }

        $paginas = self::paginas($titulo, $colunas, $linhas);

        $quantidadePaginas = count($paginas);

        $idsPaginas = range(3, 2 + $quantidadePaginas);

        $idFonte = 3 + $quantidadePaginas;

        $idsConteudos = range(
            $idFonte + 1,
            $idFonte + $quantidadePaginas
        );

        $quantidadeObjetos = $idFonte + $quantidadePaginas;

        $objetos = array_fill(
            0,
            $quantidadeObjetos + 1,
            ''
        );

        $objetos[1] =
            '<< /Type /Catalog /Pages 2 0 R >>';

        $objetos[2] =
            '<< /Type /Pages /Kids ['
            . implode(
                ' ',
                array_map(
                    static fn(int $id): string => "{$id} 0 R",
                    $idsPaginas
                )
            )
            . "] /Count {$quantidadePaginas} >>";

        foreach ($idsPaginas as $indice => $idPagina) {
            $objetos[$idPagina] =
                "<< /Type /Page /Parent 2 0 R "
                . "/MediaBox [0 0 595 842] "
                . "/Resources << /Font << "
                . "/F1 {$idFonte} 0 R "
                . ">> >> "
                . "/Contents {$idsConteudos[$indice]} 0 R >>";
        }

        /*
         * Helvetica + WinAnsiEncoding permite caracteres
         * portugueses quando o texto é convertido para Windows-1252.
         */
        $objetos[$idFonte] =
            '<< /Type /Font '
            . '/Subtype /Type1 '
            . '/BaseFont /Helvetica '
            . '/Encoding /WinAnsiEncoding >>';

        foreach ($idsConteudos as $indice => $idConteudo) {
            $stream = $paginas[$indice];

            $objetos[$idConteudo] =
                "<< /Length " . strlen($stream) . " >>\n"
                . "stream\n"
                . $stream
                . "\nendstream";
        }

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";

        $deslocamentos = [0];

        for ($id = 1; $id <= $quantidadeObjetos; $id++) {
            $deslocamentos[$id] = strlen($pdf);

            $pdf .= "{$id} 0 obj\n";
            $pdf .= $objetos[$id] . "\n";
            $pdf .= "endobj\n";
        }

        $inicioXref = strlen($pdf);

        $pdf .=
            "xref\n"
            . "0 " . ($quantidadeObjetos + 1) . "\n"
            . "0000000000 65535 f \n";

        for ($id = 1; $id <= $quantidadeObjetos; $id++) {
            $pdf .= sprintf(
                "%010d 00000 n \n",
                $deslocamentos[$id]
            );
        }

        $pdf .=
            "trailer\n"
            . "<< /Size " . ($quantidadeObjetos + 1)
            . " /Root 1 0 R >>\n"
            . "startxref\n"
            . $inicioXref
            . "\n%%EOF\n";

        return $pdf;
    }

    private static function paginas(
        string $titulo,
        array $colunas,
        array $linhas
    ): array {
        $paginasLinhas = array_chunk(
            $linhas,
            self::LINHAS_POR_PAGINA
        );

        if ($paginasLinhas === []) {
            $paginasLinhas = [[]];
        }

        $totalPaginas = count($paginasLinhas);

        $larguraTabela =
            self::LARGURA_PAGINA - (self::MARGEM * 2);

        $larguraColuna =
            $larguraTabela / count($colunas);

        $tamanhoFonte =
            count($colunas) > 6 ? 7.0 : 8.0;

        $limiteTexto = max(
            6,
            (int) floor(
                $larguraColuna / ($tamanhoFonte * 0.55)
            )
        );

        $paginas = [];

        foreach ($paginasLinhas as $indicePagina => $linhasPagina) {
            $paginas[] = self::pagina(
                $titulo,
                $colunas,
                $linhasPagina,
                $indicePagina + 1,
                $totalPaginas,
                $larguraTabela,
                $larguraColuna,
                $tamanhoFonte,
                $limiteTexto
            );
        }

        return $paginas;
    }

    private static function pagina(
        string $titulo,
        array $colunas,
        array $linhas,
        int $numeroPagina,
        int $totalPaginas,
        float $larguraTabela,
        float $larguraColuna,
        float $tamanhoFonte,
        int $limiteTexto
    ): string {
        $topoTabela = 760.0;
        $x = self::MARGEM;
        $stream = '';

        $stream .=
            "BT\n"
            . "/F1 15 Tf\n"
            . "1 0 0 1 36 806 Tm\n"
            . "("
            . self::textoPdf(self::normalizarTexto($titulo))
            . ") Tj\n"
            . "ET\n";

        $stream .=
            "BT\n"
            . "/F1 8 Tf\n"
            . "1 0 0 1 36 791 Tm\n"
            . "("
            . self::textoPdf(
                self::normalizarTexto(
                    'Gerado em '
                    . date('d/m/Y H:i')
                    . " | Página {$numeroPagina}/{$totalPaginas}"
                )
            )
            . ") Tj\n"
            . "ET\n";

        // Fundo do cabecalho.
        $stream .=
            "0.91 0.94 0.97 rg\n"
            . self::numero($x)
            . " "
            . self::numero(
                $topoTabela - self::ALTURA_CABECALHO
            )
            . " "
            . self::numero($larguraTabela)
            . " "
            . self::numero(self::ALTURA_CABECALHO)
            . " re f\n";

        // Linhas da tabela.
        $stream .=
            "0.35 0.40 0.46 RG\n"
            . "0.5 w\n";

        $stream .= self::linha(
            $x,
            $topoTabela,
            $x + $larguraTabela,
            $topoTabela
        );

        $stream .= self::linha(
            $x,
            $topoTabela - self::ALTURA_CABECALHO,
            $x + $larguraTabela,
            $topoTabela - self::ALTURA_CABECALHO
        );

        foreach ($colunas as $indice => $coluna) {
            $posicaoX =
                $x + ($indice * $larguraColuna);

            $stream .= self::linha(
                $posicaoX,
                $topoTabela,
                $posicaoX,
                $topoTabela - self::ALTURA_CABECALHO
            );

            $stream .= self::textoCelula(
                self::rotuloColuna($coluna),
                $posicaoX + 4,
                $topoTabela - 15,
                $tamanhoFonte,
                $limiteTexto
            );
        }

        $stream .= self::linha(
            $x + $larguraTabela,
            $topoTabela,
            $x + $larguraTabela,
            $topoTabela - self::ALTURA_CABECALHO
        );

        if ($linhas === []) {
            $linhas = [
                array_fill_keys($colunas, '')
            ];

            $linhas[0][$colunas[0]] =
                'Nenhum registro encontrado.';
        }

        foreach ($linhas as $indice => $linha) {
            $topoLinha =
                $topoTabela
                - self::ALTURA_CABECALHO
                - ($indice * self::ALTURA_LINHA);

            $baseLinha =
                $topoLinha - self::ALTURA_LINHA;

            if ($indice % 2 === 0) {
                $stream .=
                    "0.98 0.98 0.98 rg\n"
                    . self::numero($x)
                    . " "
                    . self::numero($baseLinha)
                    . " "
                    . self::numero($larguraTabela)
                    . " "
                    . self::numero(self::ALTURA_LINHA)
                    . " re f\n";
            }

            $stream .=
                "0.80 0.82 0.85 RG\n"
                . self::linha(
                    $x,
                    $baseLinha,
                    $x + $larguraTabela,
                    $baseLinha
                );

            foreach ($colunas as $colunaIndice => $coluna) {
                $posicaoX =
                    $x + ($colunaIndice * $larguraColuna);

                $stream .= self::linha(
                    $posicaoX,
                    $baseLinha,
                    $posicaoX,
                    $topoLinha
                );

                $stream .= self::textoCelula(
                    $linha[$coluna] ?? '',
                    $posicaoX + 4,
                    $baseLinha + 5,
                    $tamanhoFonte,
                    $limiteTexto
                );
            }

            $stream .= self::linha(
                $x + $larguraTabela,
                $baseLinha,
                $x + $larguraTabela,
                $topoLinha
            );
        }

        $baseFinal =
            $topoTabela
            - self::ALTURA_CABECALHO
            - (count($linhas) * self::ALTURA_LINHA);

        $stream .= self::linha(
            $x,
            $topoTabela,
            $x,
            $baseFinal
        );

        $stream .= self::linha(
            $x + $larguraTabela,
            $topoTabela,
            $x + $larguraTabela,
            $baseFinal
        );

        return $stream;
    }

    private static function linha(
        float $x1,
        float $y1,
        float $x2,
        float $y2
    ): string {
        return
            self::numero($x1)
            . ' '
            . self::numero($y1)
            . ' m '
            . self::numero($x2)
            . ' '
            . self::numero($y2)
            . " l S\n";
    }

    private static function textoCelula(
        mixed $valor,
        float $x,
        float $y,
        float $tamanhoFonte,
        int $limite
    ): string {
        $texto = self::normalizarTexto($valor);

        if (strlen($texto) > $limite) {
            $texto =
                substr(
                    $texto,
                    0,
                    max(1, $limite - 3)
                )
                . '...';
        }

        return
            "BT\n"
            . "0 0 0 rg\n"
            . "/F1 "
            . self::numero($tamanhoFonte)
            . " Tf\n"
            . "1 0 0 1 "
            . self::numero($x)
            . ' '
            . self::numero($y)
            . " Tm\n"
            . "("
            . self::textoPdf($texto)
            . ") Tj\n"
            . "ET\n";
    }

    private static function rotuloColuna(string $coluna): string
    {
        $rotulos = [
            'id' => 'ID',
            'animal_id' => 'Animal ID',
            'veterinario_id' => 'Veterinário ID',
            'procedimento_id' => 'Procedimento ID',
            'tutor_id' => 'Tutor ID',
            'especie_id' => 'Espécie ID',
            'usuario_id' => 'Usuário ID',

            'nome' => 'Nome',
            'nome_vacina' => 'Nome da vacina',
            'raca' => 'Raça',
            'data_nascimento' => 'Data de nascimento',
            'sexo' => 'Sexo',
            'peso' => 'Peso',
            'castrado' => 'Castrado',
            'observacoes' => 'Observações',

            'lote' => 'Lote',
            'data_aplicacao' => 'Data de aplicação',
            'data_retorno' => 'Data de retorno',

            'procedimento' => 'Procedimento',
            'descricao' => 'Descrição',
            'valor' => 'Valor',
            'valor_cobrado' => 'Valor cobrado',
            'duracao_minutos' => 'Duração (minutos)',

            'data_hora' => 'Data e hora',
            'observacoes_clinicas' => 'Observações clínicas',
            'situacao' => 'Situação',

            'cpf' => 'CPF',
            'telefone' => 'Telefone',
            'email' => 'E-mail',
            'endereco' => 'Endereço',
            'data_cliente' => 'Data do cliente',

            'crmv' => 'CRMV',
            'especialidade' => 'Especialidade',
            'ativo' => 'Ativo',
        ];

        return $rotulos[$coluna] ?? ucwords(
            str_replace('_', ' ', $coluna)
        );
    }

    private static function normalizarTexto(mixed $valor): string
    {
        if ($valor === null) {
            return '';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sim' : 'Não';
        }

        if (!is_scalar($valor)) {
            $valor = json_encode(
                $valor,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );
        }

        $texto =
            preg_replace(
                '/\s+/',
                ' ',
                (string) $valor
            ) ?? '';

        if ($texto === '0000-00-00') {
            return '-';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $texto) === 1) {
            $data = \DateTime::createFromFormat('Y-m-d', $texto);

            if ($data !== false) {
                return $data->format('d/m/Y');
            }
        }

        /*
         * O PDF usa WinAnsiEncoding.
         * Convertendo UTF-8 para Windows-1252,
         * conseguimos exibir acentos como:
         * á, é, í, ó, ú, ã, õ, ç etc.
         */
        if (function_exists('iconv')) {
            $convertido = iconv(
                'UTF-8',
                'Windows-1252//TRANSLIT//IGNORE',
                $texto
            );

            if ($convertido !== false) {
                return $convertido;
            }
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding(
                $texto,
                'Windows-1252',
                'UTF-8'
            );
        }

        return
            preg_replace(
                '/[^\x20-\x7E]/',
                '?',
                $texto
            ) ?? '';
    }

    private static function textoPdf(string $texto): string
    {
        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\\(', '\\)'],
            $texto
        );
    }

    private static function numero(float $valor): string
    {
        return
            rtrim(
                rtrim(
                    number_format(
                        $valor,
                        2,
                        '.',
                        ''
                    ),
                    '0'
                ),
                '.'
            ) ?: '0';
    }
}