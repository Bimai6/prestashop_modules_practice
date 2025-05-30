<?php
require_once _PS_ROOT_DIR_ . '/tools/guard.php';

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'Firstname Lastname';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('My module', [], 'Modules.Mymodule.Admin');
        $this->description = $this->trans('Description of my module.', [], 'Modules.Mymodule.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Mymodule.Admin');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Mymodule.Admin');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayRightColumn') &&
            $this->registerHook('displayBanner') &&
            $this->registerHook('displayFooter') &&
            Configuration::updateValue('MYMODULE_NAME', 'my module');
    }

    public function uninstall()
    {
        return (
            parent::uninstall() 
            && Configuration::deleteByName('MYMODULE_NAME')
        );
    }

    public function hookDisplayBanner($params) {
        return $this->display(__FILE__, 'views/templates/hook/displayBanner.tpl');
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->context->smarty->assign(
            [
                'my_module_name' => Configuration::get('MYMODULE_NAME'),
                'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display'),
                'my_module_message' => $this->l('This is a simple text message') // Do not forget to enclose your strings in the l() translation method
            ]
        );

        return $this->display(__FILE__, 'mymodule.tpl');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayFooter($params)
    {
        $items = [
            'Productos' => ['Ofertas', 'Novedades'],
            'Nuestra Empresa' => ['Envío', 'Aviso legal', 'Sobre nosotros'],
            'Su cuenta' => ['Iniciar sesión', 'Crear cuenta'],
            'Información de la cuenta' => ['Tienda de ejemplo', 'España']
        ];

        $this->context->smarty->assign('items', $items);
        return $this->display(__FILE__, '/views/templates/hook/displayFooter.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'mymodule-style',
            'modules/' . $this->name . '/views/css/mymodule.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        $this->context->controller->registerJavascript(
            'mymodule-javascript',
            'modules/' . $this->name . '/views/js/mymodule.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }
}
