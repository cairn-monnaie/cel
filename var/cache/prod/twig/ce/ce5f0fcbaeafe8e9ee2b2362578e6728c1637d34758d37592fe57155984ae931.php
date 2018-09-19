<?php

/* CairnUserBundle::layout-pdf.html.twig */
class __TwigTemplate_b61526ce8e47472477aca586710ba9186673ed8046528081a05d8388b578f611 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("::base.html.twig", "CairnUserBundle::layout-pdf.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'fos_user_content' => array($this, 'block_fos_user_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        // line 7
        echo "    <div>
        ";
        // line 8
        $this->displayBlock('fos_user_content', $context, $blocks);
        // line 9
        echo "    </div>
";
    }

    // line 8
    public function block_fos_user_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "CairnUserBundle::layout-pdf.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 8,  43 => 9,  41 => 8,  38 => 7,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle::layout-pdf.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/layout-pdf.html.twig");
    }
}
