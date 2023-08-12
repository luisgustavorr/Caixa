<script>
    $(function() {
        $(document).tooltip();
    });
</script>
<form class="modal modal_adicionar_produto">
    <div class="first_input_row">
        <div class="inputs input_nome_produto_add">
            <label for="">Nome do produto:</label><br />
            <input class="oders_inputs" type="text" placeholder="Digite o nome do Produto" name="nome_produto_add" id="nome_produto_add" required>
        </div>
        <div class="inputs input_codigo_produto_add">
            <label for="">Código de barras do produto:</label><br />
            <input maxlength="13" class="oders_inputs" type="text" placeholder="Digite ou leia o Código de barras do Produto" name="codigo_barras_produto_add" id="codigo_barras_produto_add" required>
        </div>


    </div>
    <div class="second_input_row">
        <div class="inputs input_codigo_produto_add">
            <label for="">Código do produto:</label><br />
            <input class="oders_inputs" type="text" placeholder="Digite o Código de barras do Produto" name="codigo_produto_add" id="codigo_produto_add" required>
        </div>
        <div class="inputs input_preco_produto_add">
            <label for="">Valor por UN ou g:</label><br />
            <input class="oders_inputs" type="text" placeholder="Digite o preço do Produto" name="preco_produto_add" id="preco_produto_add" required>
        </div>

        <div class="inputs input_por_peso">
            <label for="">É por grama?</label><br />
            <div class="inputs_radio_father">
                <label for="sim">Sim</label>
                <input class="oders_inputs" type="radio" name="produto_por_peso" required value="1" id="sim">
                <label for="nao">Não</label>
                <input class="oders_inputs" type="radio" name="produto_por_peso" value="0" id="nao">
            </div>

        </div>
        <button id="finalziar_button_add">Finalizar</button>
    </div>
</form>
<form class="modal modal_funcionarios">
    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Nome</th>
                <th>Administrador</th>
                <th>Loja</th>
                <th>Excluir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $caixas = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores`");
            $caixas->execute();
            $caixas = $caixas->fetchAll();
            foreach ($caixas as $key => $value) {
                $value['administrador'] == 1 ? $adm = 'Sim' : $adm = 'Não';
                echo '<tr value="' . $value['codigo'] . '">
                                <td>' . ucfirst($value['codigo']) . '</td>

                                <td>' . ucfirst($value['nome']) . '</td>
                                <td>' . $adm . '</td>
                                <td>' . $value["caixa"] . '</td>

                                <td><i pessoa="' . $value['id'] . '" class="fa-solid fa-trash-can"></i></td>

                                </tr>';
            }
            ?>
        </tbody>
    </table>
 
    <div class="inputs_add_usuario">
        <div class="inputs_father_user">
            <label for="input_add_usuario_codigo">Código:</label>
            <input type="text" name="input_add_usuario_codigo" required id="input_add_usuario_codigo" class="oders_inputs">
        </div>
        <div class="inputs_father_user">
            <label for="input_add_usuario_nome">Nome:</label>
            <input type="text" name="input_add_usuario_nome" required id="input_add_usuario_nome" class="oders_inputs">
        </div>
        <span >Caixa(s) Selecionado(s) : <select name="select_caixa_add_usuario" id="select_caixa_add_usuario">
     
            <?php
            $caixas = \MySql::conectar()->prepare("SELECT * FROM `tb_caixas`");
            $caixas->execute();
            $caixas = $caixas->fetchAll();
            foreach ($caixas as $key => $value) {
                echo '<option value="' . $value['caixa'] . '">' . ucfirst($value['caixa']) . '</option>';
            }
            ?>
        </select></span>
        <div class=" input_por_peso">
            <label for="">Administrador?</label><br />
            <div class="inputs_radio_father">
                <label for="sim">Sim</label>
                <input class="oders_inputs" type="radio" name="add_funcionario" required value="1" id="sim">
                <label for="nao">Não</label>
                <input class="oders_inputs" type="radio" name="add_funcionario" value="0" id="nao">
            </div>

        </div>
        <button id="add_usuario">Adicionar</button>
    </div>

</form>

</div>
<aside id="sidebar">
    <span class="princip_span" onclick="abrirModal('modal_funcionarios')"> Funcionários <i class="fa-solid fa-user-plus"></i></span>
    <span class="princip_span" id="add_produto_opener" onclick="abrirModal('modal_adicionar_produto')">Adicionar Produto <i class="fa-solid fa-plus"></i></span>

    <span class="princip_span" id="add_caixa_opener">Adicionar Caixa <i class="fa-solid fa-angle-down"></i></span>

    <form action="" id="adicionar_caixa">
        <span class="princip_span">Nome</span><br><input id="nome_caixa" placeholder="Digite o nome desse caixa" type="text" required><br>
        <span class="princip_span">Troco Inicial</span><br><input onKeyUp="mascaraMoeda(this, event)" id="troco_inicial" placeholder="Digite o troco inicial desse caixa" type="text" required><br>
        <button>Adicionar</button>
    </form>


    <span class="princip_span">Caixa(s) Selecionado(s) : <select name="select_caixa" id="select_caixa">
            <option value="todos">Todos</option>
            <?php
            $caixas = \MySql::conectar()->prepare("SELECT * FROM `tb_caixas`");
            $caixas->execute();
            $caixas = $caixas->fetchAll();
            foreach ($caixas as $key => $value) {
                echo '<option value="' . $value['caixa'] . '">' . ucfirst($value['caixa']) . '</option>';
            }
            ?>
        </select></span>

    <form id="form_equip">

        <span class="princip_span">Equipamentos do caixa <red>:</red></span>
        <span class="princip_span">Nome Impressora</span>
        <input type="text" id="nome_impressora">
        <span class="princip_span">Porta serial da balança</span>
        <input type="text" id="porta_balanca">
        <span class="princip_span">Frequencia da balança</span>
        <input type="text" id="freq_balanca">
        <button>Salvar</button>
    </form>
    <span id="caixa_remover">
        <red>Remover Caixa <red>
    </span>
    <span class="princip_span" onclick="atualizarSistema('modal_anotar_pedido')">Atualizar Sistema</span>
</aside>
<fundo></fundo>
<form action="" class="modal modal_fechar_caixa">
    <h3><i id="print_fechamento" class="fa-solid fa-print"></i> Fechamento do caixa: <red><select id="caixa_ser_fechado">
                <?php
                $caixas = \MySql::conectar()->prepare("SELECT * FROM `tb_caixas` WHERE valor_atual > 0 AND valor_atual != troco_inicial");
                $caixas->execute();
                $caixas = $caixas->fetchAll();
                foreach ($caixas as $key => $value) {
                    echo '<option value="' . $value['caixa'] . '">' . ucfirst($value['caixa']) . '</option>';
                }
                ?>
            </select></red>
    </h3>
    <div class="first_row">
        <div class="left_side">
            Data do Fechamento:
            <span><?php echo date('d/m/Y') ?></span>
        </div>
        <div class="right_side">
            <div class="input_codigo_user_father">
                <label for="input_codigo_user">Código:</label>
                <input type="text" class="oders_inputs " name="input_codigo_user" id="input_codigo_user">
            </div>
            <div class="input_nome_user_father">
                <label for="input_nome_user">Nome do Funcionário:</label>
                <input type="text" class="oders_inputs tag_user" name="input_nome_user" id="input_nome_user">
            </div>
        </div>

    </div>
    <div class="valores_informados_box">
        <span class="valores_informados_title">Valores Informados:</span>
        <div class="body_valores">
            <div class="first_column">

                <div class="input_valores">
                    <label for="dinheiro_informadas">Dinheiro: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="valores_informados input_princip_completo oders_inputs" name="dinheiro_informadas" id="dinheiro_informadas">
                </div>
                <div class="input_valores">
                    <label for="pix_informadas">Pix: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="valores_informados input_princip_completo oders_inputs" name="pix_informadas" id="pix_informadas">
                </div>
            </div>
            <div class="first_column">
                <div class="input_valores">
                    <label for="moedas_informadas">Cartão: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="valores_informados input_princip_completo oders_inputs" name="moedas_informadas" id="moedas_informadas">
                </div>

                <!-- <div class="input_valores">
                    <label for="pix_informadas">Vale-Ticket </label>
                    <input onKeyUp="mascaraMoeda(this, event)"type="text" class="input_princip oders_inputs"name="pix_informadas" id="pix_informadas">
                    <input onKeyUp="mascaraMoeda(this, event)"type="text"  class="quantidade quantidade_pix oders_inputs">
                </div> -->
                <div class="input_valores">
                    <label for="dinheiro_informadas">Pgto/Sangria: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="valores_informados input_princip_completo oders_inputs" name="dinheiro_informadas" id="dinheiro_informadas">
                </div>
            </div>
            <div class="second_column">

            </div>
        </div>
        <span class="valores_informados_footer">Valor Total: <red> R$00,00</red></span>
    </div>
    <h4 id="mostrar">Mostrar valores apurados</h4>
    <div class="valores_informados_box">
        <span class="valores_informados_title">Valores Apurados:</span>
        <div class="body_valores">
            <div class="first_column">
                <div class="input_valores">
                    <label for="troco_inicial_fechar">Troco Inicial: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class=" input_princip_completo oders_inputs" name="troco_inicial_fechar" id="troco_inicial_fechar">
                </div>
                <div class="input_valores">
                    <label for="total_vendas">Total de Vendas: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class=" input_princip_completo oders_inputs" name="total_vendas" id="total_vendas">
                </div>
                <div class="input_valores">
                    <label for="troco_final">Troco Final: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class=" input_princip_completo oders_inputs" name="troco_final" id="troco_final">
                </div>
            </div>
            <div class="first_column">
                <div class="input_valores">
                    <label for="total_apurado">Total Apurado: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="input_princip_completo oders_inputs" name="total_apurado" id="total_apurado">
                </div>
                <div class="input_valores">
                    <label for="total_informado">Total Informado: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="input_princip_completo oders_inputs" name="total_informado" id="total_informado">
                </div>
                <div class="input_valores">
                    <label for="diferenca">Diferença: </label>
                    <input onKeyUp="mascaraMoeda(this, event)" type="text" class="input_princip_completo oders_inputs" name="diferenca" id="diferenca">
                </div>
            </div>
            <div class="second_column">

            </div>
        </div>
        <span class="valores_apurados_footer">Valor Total: <red> R$00,00</red> </span>
    </div>
    <!-- <div class="valores_apurados_box">
        <span class="valores_apurados_title">Valores Informados:</span>
        <span class="valores_apurados_footer">Valor Total: <red> R$00,00</red></span>
    </div> -->
</form>
<div class="header_section">
    <div class="left_side">
        <section style="width: 40%;">
            Selecione o Período:
            <div class="inputs_header_section">
                <input type="date" class="datas" name="data_minima" value="<?php echo  date('Y-m-d') ?>" max="<?php echo  date('Y-m-d') ?>" id="data_minima">
                <input type="date" class="datas" value="<?php echo  date('Y-m-d') ?>" name="data_maxima" max="<?php echo  date('Y-m-d') ?>" id="data_maxima">

            </div>
        </section>

    </div>
    <div class="right_side">
        <div class="right_subdivision">
            <h3>Algumas Métricas:</h3>
            <span>Tipo de Pagamento mais recorrente:</span>
            <red class="pagamento_recorrente"><?php \Models\PainelControleModel::buscarDados('formaPagamentoMaisRepetida') ?></red>
            <span>Quantidade de Vendas no período::</span>
            <red class="quant_vendas"><?php \Models\PainelControleModel::buscarDados('quantidadeVendas') ?> Vendas</red>
            <span>Produto Mais vendido no período::</span>
            <red class="top_produto"><?php \Models\PainelControleModel::buscarDados('produtoMaisVendido') ?></red>
            <span>Total valor de vendas período::</span>
            <red class="valor_total">R$<?php \Models\PainelControleModel::buscarDados('totalValor') ?></red>
        </div>
        <div class="left_subdivision">
            <h3>Realizar Fechamento do caixa</h3>
            <span>Do dia <?php echo date('d/m/Y') ?></span>
            <button onclick="abrirModal('modal_fechar_caixa')"><i class="fa-solid fa-cart-shopping"></i> Fechar Caixa</button>
            <div class="selection_switch">
                <span>Vendas Feitas</span>
                <switch>
                    <dot style="float: left;"></dot>
                </switch>
                <span>Produtos Vendidos</span>
            </div>
        </div>
    </div>

</div>
<div class="tabela_father">
    <div class="tabela_header">

        <i id="voltar_semana" onclick="mudarTempo(this)" class="fa-solid fa-angle-left modificadores_tempo "></i> <span>Vendas no dia: <yellow> <?php echo date('d/m/Y') ?></yellow> <i onclick='gerarPDFFullFunction(this)' class="gerar_pdf fa-regular fa-file-pdf"></i></span><i onclick="mudarTempo(this)" id='adiantar_semana' class="fa-solid fa-angle-right modificadores_tempo adiantar_semana"></i>
    </div>
    <table id="table_tabela">
        <thead>
            <tr>
                <th>Data Venda</th>
                <th>Valor Total</th>
                <th>Produtos na Venda</th>
                <th>Método de Pagamento</th>
            </tr>
        </thead>
        <tbody>
            <?php \Models\PainelControleModel::formarTabela() ?>

        </tbody>
    </table>
</div>

<script src="<?php echo INCLUDE_PATH ?>js/criar_pdf_tabela.js"></script>
<script src="<?php echo INCLUDE_PATH ?>js/atualizar_sistema.js"></script>

<script src="<?php echo INCLUDE_PATH ?>js/index.js"></script>
<script src="<?php echo INCLUDE_PATH ?>js/fechar_caixa.js"></script>

<script src="<?php echo INCLUDE_PATH ?>js/posts_senders.js"></script>