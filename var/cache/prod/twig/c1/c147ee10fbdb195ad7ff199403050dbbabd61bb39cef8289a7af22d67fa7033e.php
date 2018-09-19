<?php

/* CairnUserBundle:Registration:index.html.twig */
class __TwigTemplate_68ca67aec013257e5354ee5b347ad4d41ab88d24c3f2944ef40e0a1fb16d6b96 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Registration:index.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN")) {
            // line 7
            echo "        <h1> Qui est-il ?</h1>

        ";
            // line 9
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 10
                echo "            <li><a href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "superAdmin"));
                echo "\" > Administrateur </a></li>
            <li><a href=\"";
                // line 11
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "localGroup"));
                echo "\" > Groupe local </a></li>
        ";
            }
            // line 13
            echo "        <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "pro"));
            echo "\" > Professionnel </a></li>
        <li><a href=\"";
            // line 14
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "adherent"));
            echo "\" > Particulier </a></li>

    ";
        }
        // line 17
        echo "
    ";
        // line 18
        if ( !$this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            // line 19
            echo "        <h1> Qui êtes-vous ? </h1>
            <div>
                <ul>
                    <li><a href=\"";
            // line 22
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "pro"));
            echo "\" > Professionnel </a></li>
                    <li><a href=\"";
            // line 23
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "adherent"));
            echo "\" > Particulier </a></li>
                </ul>
            </div>
    ";
        }
        // line 27
        echo "         
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Registration:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 27,  79 => 23,  75 => 22,  70 => 19,  68 => 18,  65 => 17,  59 => 14,  54 => 13,  49 => 11,  44 => 10,  42 => 9,  38 => 7,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Registration:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Registration/index.html.twig");
    }
}
