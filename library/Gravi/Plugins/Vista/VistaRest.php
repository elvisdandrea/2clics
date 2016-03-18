<?php

/**
 * Class VistaRest
 *
 * Implementation of Vista Requests
 */
class VistaRest extends Vista {

    /**
     * Class setup
     */
    public function __construct() {
        parent::__construct();
    }

    public function getDestaques() {

        $this->setVistaMethod('imoveis', 'destaques');
        $this->addFieldParam('Categoria');
        $this->addFieldParam('Codigo');
        $this->addFieldParam('Bairro');
        $this->addFieldParam('Cidade');
        $this->addFieldParam('Dormitorios');
        $this->addFieldParam('BanheiroSocialQtd');

        $this->execute();

    }

    public function getListasBusca() {

        $buscaSession = new Zend_Session_Namespace('buscas');

        if (isset($buscaSession->busca)) return $buscaSession->busca;

        $this->setVistaMethod('imoveis', 'listarConteudo');
        $this->addFieldParam('Categoria');
        $this->addFieldParam('Bairro');
        $this->addFieldParam('Cidade');
        $this->addFieldParam('BanheiroSocialQtd');

        $this->execute();

        $result = $this->getResult();
        $buscaSession->busca = $result;
        return $result;
    }

    public function buscaImoveis(array $filtros, array $pagination = array(), $order = array()) {

        $this->setVistaMethod('imoveis', 'listar');

        $this->addFieldParam('Codigo');
        $this->addFieldParam('Categoria');
        $this->addFieldParam('Bairro');
        $this->addFieldParam('Cidade');
        $this->addFieldParam('ValorVenda');
        $this->addFieldParam('ValorOferta');
        $this->addFieldParam('Dormitorios');
        $this->addFieldParam('BanheiroSocialQtd');
        $this->addFieldParam('Empreendimento');
        $this->addFieldParam('Suites');
        $this->addFieldParam('Vagas');
        $this->addFieldParam('AreaTotal');
        $this->addFieldParam('AreaUtil');
        $this->addFieldParam('AreaTerreno');
        $this->addFieldParam('DimensoesTerreno');
        $this->addFieldParam('FotoDestaque');
        $this->addFieldParam('FotoDestaquePequena');
        $this->addFieldParam('Latitude');
        $this->addFieldParam('Longitude');

        if (isset($pagination['pagina']) && isset($pagination['quantidade'])) {
            $this->setPaginationParam($pagination['pagina'], $pagination['quantidade']);
        }

        if (count($order) > 0) {
            foreach ($order as $field => $value) $this->addOrderParam($field, $value);
        }

        empty($filtros) || $this->addFilterParam($filtros);

        $this->execute();
    }

    public function getDadosImovel($imoCodigo){
        $this->setVistaMethod('imoveis', 'detalhes');

        $this->addParam('imovel', $imoCodigo);

        $this->addFieldParam('Codigo');
        $this->addFieldParam('Categoria');
        $this->addFieldParam('Bairro');
        $this->addFieldParam('Cidade');
        $this->addFieldParam('ValorVenda');
        $this->addFieldParam('ValorOferta');
        $this->addFieldParam('Dormitorios');
        $this->addFieldParam('Empreendimento');
        $this->addFieldParam('Descricao');
        $this->addFieldParam('BanheiroSocialQtd');
        $this->addFieldParam('Suites');
        $this->addFieldParam('Vagas');
        $this->addFieldParam('AreaTotal');
        $this->addFieldParam('AreaUtil');
        $this->addFieldParam('AreaTerreno');
        $this->addFieldParam('DimensoesTerreno');
        $this->addFieldParam(array('Foto' => array('Foto', 'FotoPequena', 'Destaque')));
        $this->addFieldParam('Caracteristicas');
        $this->addFieldParam('InfraEstrutura');
        $this->addFieldParam('Latitude');
        $this->addFieldParam('Longitude');

        $this->execute();
    }

    public function getAuthEmail() {

        $this->setVistaMethod(false, 'emailauth');
        $this->setEmptyBody(true);
        $this->execute();

    }

    public function getDadosAgencias() {

        $this->setVistaMethod('agencias', 'listar');
        $this->addFieldParam('Codigo');
        $this->addFieldParam('Nome');
        $this->addFieldParam('E-mail');
        $this->addFieldParam('Fone');

        $this->execute();
    }

}