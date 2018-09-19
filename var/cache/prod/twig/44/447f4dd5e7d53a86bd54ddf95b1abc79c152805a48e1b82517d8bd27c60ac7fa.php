<?php

/* layout.html.twig */
class __TwigTemplate_f87205245e002fdece216a407d6fc64fed5c764a809026b78fc601ec4ea20471 extends Twig_Template
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
            'fos_user_content' => array($this, 'block_fos_user_content'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "
<!DOCTYPE html>

<html>
    <head>
        <meta charset=\"utf-8\" />
        ";
        // line 8
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 11
        echo "        <meta name=\"viewport\" content=\"width=device-width\" />
        <title>";
        // line 12
        $this->displayBlock('title', $context, $blocks);
        echo " </title>

    </head>
    <body>
        <div id=\"main_wrapper\">
            <header>
                <div class=\"top-left\">
                    <img src=\"";
        // line 19
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
        // line 28
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\" alt=\"Notifications\">
                        </div>
                        ";
        // line 30
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array(), "any", false, true), "image", array(), "any", false, true), "url", array(), "any", true, true)) {
            // line 31
            echo "                            <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl(((("uploads/img/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "image", array()), "id", array())) . ".") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "image", array()), "url", array()))), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "image", array()), "alt", array()), "html", null, true);
            echo "\"> 

                        ";
        } else {
            // line 34
            echo "                            <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
            echo "\"alt=\"Logo utilisateur\"> 

                        ";
        }
        // line 37
        echo "                    </div>
                    <div id=\"logout\">
                        ";
        // line 39
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            // line 40
            echo "                            Connecté en tant que ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "username", array()), "html", null, true);
            echo "
                            -
                            <a href=\"";
            // line 42
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("fos_user_security_logout");
            echo "\">
                                <img src=\"";
            // line 43
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
            echo "\" alt=\"Déconnexion\"></a>
                        ";
        } else {
            // line 45
            echo "                            <a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("fos_user_security_login");
            echo "\">Connexion</a>
                        ";
        }
        // line 47
        echo "                    </div>
                </div>
            </header> 
            ";
        // line 50
        $this->displayBlock('fos_user_content', $context, $blocks);
        // line 51
        echo "            ";
        $this->displayBlock('body', $context, $blocks);
        // line 52
        echo "        </div>
        ";
        // line 53
        $this->displayBlock('javascripts', $context, $blocks);
        // line 57
        echo "    </body>
    <footer>
        <nav>
            <ul>
                <li><a class=menu-item href=\"#\"> Nos permanences <img src=\"";
        // line 61
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
                <li><a class=menu-item href=\"#\"> Site du Cairn <img src=\"";
        // line 62
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
                <li><a class=menu-item href=\"#\"> Réseaux sociaux <img src=\"";
        // line 63
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
                <li><a class=menu-item href=\"#\"> Contacts <img src=\"";
        // line 64
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("apple-touch-icon.png"), "html", null, true);
        echo "\"></a></li>
            </ul>
        </nav>

    </footer>
</html>
";
    }

    // line 8
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 9
        echo "        <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" />
        ";
    }

    // line 12
    public function block_title($context, array $blocks = array())
    {
        echo " Paye ton Cairn ";
    }

    // line 50
    public function block_fos_user_content($context, array $blocks = array())
    {
    }

    // line 51
    public function block_body($context, array $blocks = array())
    {
    }

    // line 53
    public function block_javascripts($context, array $blocks = array())
    {
        // line 54
        echo "         <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
            <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>
        ";
    }

    public function getTemplateName()
    {
        return "layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  186 => 54,  183 => 53,  178 => 51,  173 => 50,  167 => 12,  160 => 9,  157 => 8,  146 => 64,  142 => 63,  138 => 62,  134 => 61,  128 => 57,  126 => 53,  123 => 52,  120 => 51,  118 => 50,  113 => 47,  107 => 45,  102 => 43,  98 => 42,  92 => 40,  90 => 39,  86 => 37,  79 => 34,  70 => 31,  68 => 30,  63 => 28,  51 => 19,  41 => 12,  38 => 11,  36 => 8,  28 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "layout.html.twig", "/var/www/Symfony/CairnB2B/app/Resources/views/layout.html.twig");
    }
}
