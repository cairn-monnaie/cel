<?php

/* CairnUserCyclosBundle::layout.html.twig */
class __TwigTemplate_b9c20c03a802469dfecc2f561f0d2f4abb0fe40b11034ba228443489fa1e968a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserCyclosBundle::layout.html.twig", 3);
        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
";
    }

    // line 9
    public function block_body($context, array $blocks = array())
    {
        // line 10
        echo "        ";
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 11
            echo "            ";
            $this->displayParentBlock("body", $context, $blocks);
            echo "
        ";
        } else {
            // line 13
            echo "            Access denied 
        ";
        }
        // line 15
        echo "
    ";
    }

    // line 19
    public function block_javascripts($context, array $blocks = array())
    {
        // line 20
        echo "
<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 20,  65 => 19,  60 => 15,  56 => 13,  50 => 11,  47 => 10,  44 => 9,  37 => 6,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle::layout.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/layout.html.twig");
    }
}
