<?php

/* TwigBundle:Exception:error403.html.twig */
class __TwigTemplate_37dfe413531d0459207e6a577a431757a370084cc823e41350af85983fc44fd7 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 2
        $this->parent = $this->loadTemplate("layout.html.twig", "TwigBundle:Exception:error403.html.twig", 2);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'aftercontainer' => array($this, 'block_aftercontainer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "    <h3 class=\"header center\">Oups, tu n'as pas le droit de venir ici ...</h3>

    ";
        // line 7
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("IS_AUTHENTICATED_FULLY")) {
            // line 8
            echo "
    ";
        }
        // line 10
        echo "
    <p>
        La page demandée n'a pas pu être chargée avec ton profil actuel. <br>
        Il te manque sans doute une formation ou d'intégrer une commission ?
    </p>
";
    }

    // line 17
    public function block_aftercontainer($context, array $blocks = array())
    {
        // line 18
        echo "    <div class=\"center-align\">
        <img class=\"responsive-img\" src=\"";
        // line 19
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
        echo "\" alt=\"Logo utilisateur\" /> 
    </div>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:error403.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 19,  58 => 18,  55 => 17,  46 => 10,  42 => 8,  40 => 7,  36 => 5,  33 => 4,  15 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:error403.html.twig", "/var/www/Symfony/CairnB2B/app/Resources/TwigBundle/views/Exception/error403.html.twig");
    }
}
