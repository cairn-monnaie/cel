<?php

/* CairnUserBundle:Pro:view.html.twig */
class __TwigTemplate_d34d0ce6f230b325f464a8f14c7a9e6bb15bbf8c946e1b712d8feb8f74795c95 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Pro:view.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
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
    public function block_title($context, array $blocks = array())
    {
    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 8
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("pro.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
";
    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo " 
    
    ";
        // line 15
        $context["card"] = twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "card", array());
        // line 16
        echo "    <div class=\"profile_wrapper\">
        <ul>
            <li>";
        // line 18
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "name", array()), "html", null, true);
        echo "</li>                
            <li>";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "username", array()), "html", null, true);
        echo " </li>
            <li>";
        // line 20
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "email", array()), "html", null, true);
        echo "</li>
            <li>
                <div id=\"user_address\">                
                    ";
        // line 23
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "street", array()), "html", null, true);
        echo " </br>
                    ";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "zipCity", array()), "zipCode", array()), "html", null, true);
        echo " </br>
                    ";
        // line 25
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "zipCity", array()), "city", array()), "html", null, true);
        echo " 
                </div>
            </li>
            <li>";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "description", array()), "html", null, true);
        echo " </li>
            <li>
                 ";
        // line 30
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "image", array(), "any", false, true), "url", array(), "any", true, true)) {
            // line 31
            echo "                     <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl(((("uploads/img/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "image", array()), "id", array())) . ".") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "image", array()), "url", array()))), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "image", array()), "alt", array()), "html", null, true);
            echo "\"> 
                 ";
        } else {
            // line 33
            echo "                     <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
            echo "\"alt=\"Logo utilisateur\"> 
                 ";
        }
        // line 35
        echo "            </li>
        </ul>
        

        ";
        // line 39
        if ((twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "hasReferent", array(0 => twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array())), "method") || (($context["user"] ?? null) == twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array())))) {
            echo " 
            <a href=\"";
            // line 40
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_home", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
            echo "\">Carte de sécurité Cairn</a>
            <a href=\"";
            // line 41
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_remove", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
            echo "\">Fermer l'espace membre</a>

            <div class=\"accounts_tab\">
                ";
            // line 44
            echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_table.html.twig", array("accounts" => ($context["accounts"] ?? null)));
            echo "
            </div>

            ";
            // line 47
            if (twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "hasReferent", array(0 => twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array())), "method")) {
                // line 48
                echo "                ";
                if ((twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "enabled", array()) == true)) {
                    // line 49
                    echo "                    <a href=\"";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_block", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
                    echo "\">Bloquer l'accès à la plateforme</a>
                ";
                } else {
                    // line 51
                    echo "                    <a href=\"";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_activate", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
                    echo "\">Autoriser l'accès à la plateforme</a>
                ";
                }
                // line 53
                echo "
            ";
            }
            // line 55
            echo " 
            ";
            // line 56
            if ((twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()) == twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "id", array()))) {
                // line 57
                echo "                <a href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_password_change");
                echo "\">Modifier son mot de passe</a>
            ";
            }
            // line 59
            echo "        ";
        }
        // line 60
        echo "

    </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pro:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  178 => 60,  175 => 59,  169 => 57,  167 => 56,  164 => 55,  160 => 53,  154 => 51,  148 => 49,  145 => 48,  143 => 47,  137 => 44,  131 => 41,  127 => 40,  123 => 39,  117 => 35,  111 => 33,  103 => 31,  101 => 30,  96 => 28,  90 => 25,  86 => 24,  82 => 23,  76 => 20,  72 => 19,  68 => 18,  64 => 16,  62 => 15,  56 => 13,  53 => 12,  47 => 9,  42 => 8,  39 => 7,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pro:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/view.html.twig");
    }
}
