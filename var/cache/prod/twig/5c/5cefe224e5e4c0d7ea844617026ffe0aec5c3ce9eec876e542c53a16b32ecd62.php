<?php

/* layout.html */
class __TwigTemplate_d90516faa5049f83f6c403e91bc0435f099b873fb64ba10c48442cc43350aede extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>

<html>
    <head>
        <meta charset=\"utf-8\" />
        ";
        // line 6
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 9
        echo "        <meta name=\"viewport\" content=\"width=device-width\" />
        <title>";
        // line 10
        $this->displayBlock('title', $context, $blocks);
        echo " </title>

    </head>
    <body>
        <div id=\"main_wrapper\">
            <header>
                <div class=\"top-left\">
                    <img src=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\" alt=\"Logo du Cairn\">
                    <div id=\"titles\">
                        <h1> Le Cairn </h1>
                        <h2> Monnaie Locale Complémentaire </h2>
                    </div>
                </div>
                <div class=\"top-right\">
                    <div id=\"logo_user\">
                        <div class=\"notifications\">
                            <img  src=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\" alt=\"Notifications\">
                        </div>
                        ";
        // line 28
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array(), "any", false, true), "image", array(), "any", false, true), "url", array(), "any", true, true)) {
            // line 29
            echo "                            <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl(((("uploads/img/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "image", array()), "id", array())) . ".") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "image", array()), "url", array()))), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "image", array()), "alt", array()), "html", null, true);
            echo "\"> 

                        ";
        } else {
            // line 32
            echo "                            <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
            echo "\"alt=\"Logo utilisateur\"> 

                        ";
        }
        // line 35
        echo "                    </div>
                    <div id=\"logout\">
                        <img src=\"";
        // line 37
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\" alt=\"Déconnexion\">
                        <a href=\"#\" class=\"logout_button\"> Deconnexion </a> 
                    </div>
                </div>
            </header> 
            ";
        // line 42
        $this->displayBlock('body', $context, $blocks);
        // line 43
        echo "        </div>
        ";
        // line 44
        $this->displayBlock('javascripts', $context, $blocks);
        // line 48
        echo "    </body>
    <footer>
        <nav>
            <ul>
                <li><a class=menu-item href=\"#\"> Nos permanences <img src=\"";
        // line 52
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
                <li><a class=menu-item href=\"#\"> Site du Cairn <img src=\"";
        // line 53
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
                <li><a class=menu-item href=\"#\"> Réseaux sociaux <img src=\"";
        // line 54
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
                <li><a class=menu-item href=\"#\"> Contacts <img src=\"";
        // line 55
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
            </ul>
        </nav>

    </footer>
</html>
";
    }

    // line 6
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 7
        echo "        <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" />
        ";
    }

    // line 10
    public function block_title($context, array $blocks = array())
    {
        echo " Paye ton Cairn ";
    }

    // line 42
    public function block_body($context, array $blocks = array())
    {
    }

    // line 44
    public function block_javascripts($context, array $blocks = array())
    {
        // line 45
        echo "         <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
            <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>
        ";
    }

    public function getTemplateName()
    {
        return "layout.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  156 => 45,  153 => 44,  148 => 42,  142 => 10,  135 => 7,  132 => 6,  121 => 55,  117 => 54,  113 => 53,  109 => 52,  103 => 48,  101 => 44,  98 => 43,  96 => 42,  88 => 37,  84 => 35,  77 => 32,  68 => 29,  66 => 28,  61 => 26,  49 => 17,  39 => 10,  36 => 9,  34 => 6,  27 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "layout.html", "/var/www/Symfony/CairnB2B/app/Resources/views/layout.html");
    }
}
