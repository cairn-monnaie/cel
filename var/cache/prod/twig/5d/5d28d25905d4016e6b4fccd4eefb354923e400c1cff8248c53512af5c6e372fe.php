<?php

/* TwigBundle:Exception:error404.html.twig */
class __TwigTemplate_c228e76c72040049d239b19784fbd0fb272a1ccbbf0dc0bbc21eb67cafaf222a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 2
        $this->parent = $this->loadTemplate("layout.html.twig", "TwigBundle:Exception:error404.html.twig", 2);
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
        echo "    <h3 class=\"header center\">Oups, nous n'avons pas trouvé ce que tu cherches</h3>

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
        La page demandée n'a pas pu être chargée.
        Vérifie l'URL ou <a href=\"";
        // line 13
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_welcome");
        echo "\">retourne à l'accueil</a>.
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
        return "TwigBundle:Exception:error404.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 19,  61 => 18,  58 => 17,  51 => 13,  46 => 10,  42 => 8,  40 => 7,  36 => 5,  33 => 4,  15 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:error404.html.twig", "/var/www/Symfony/CairnB2B/app/Resources/TwigBundle/views/Exception/error404.html.twig");
    }
}
