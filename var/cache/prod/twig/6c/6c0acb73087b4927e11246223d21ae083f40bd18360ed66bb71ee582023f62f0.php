<?php

/* CairnUserBundle::layout.html.twig */
class __TwigTemplate_ed6830d86f9eab6be5e7b7ad874b2a06687f34bb07a4f556ec3d1cf2d7e695c8 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("::layout.html.twig", "CairnUserBundle::layout.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::layout.html.twig";
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
    public function block_body($context, array $blocks = array())
    {
        // line 8
        echo "
<nav>
    ";
        // line 10
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN")) {
            // line 11
            echo "
    <ul>
        <li id=\"id_welcome\" class=\"menu_depliant\">
            <a class=\"menu-item\" href=\"";
            // line 14
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_welcome");
            echo "\">Accueil</a>
        </li>
        <li>    
            <a class=\"menu-item\" href=\"";
            // line 17
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Situation </a>
        </li>

       ";
            // line 23
            echo "        <li>    
            <a class=\"menu-item\" href=\"";
            // line 24
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_home");
            echo "\">Les membres</a>
        </li>

        <li>
            <a class=\"menu-item\" href=\"#\">Opérations</a>
             <div class=\"menu-content\">
                <ul class=\"menu_list\">
                    ";
            // line 31
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 32
                echo "                        <li><a class=\"menu-subitem\" href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "transaction"));
                echo "\"> Virements </a></li>
                    ";
            }
            // line 34
            echo "                    <li><a class=\"menu-subitem\" href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "conversion"));
            echo "\"> Conversion </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 35
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "deposit"));
            echo "\"> Dépôt </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 36
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "withdrawal"));
            echo "\"> Retrait </a></li>

                </ul>
            </div>

        </li>
        <li><a class=\"menu-item\" href=\"";
            // line 42
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Profil </a></li>
        

        ";
            // line 45
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 46
                echo "            <li><a class=\"menu-item\" href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_config_home");
                echo "\">Espace configuration</a></li>
        ";
            }
            // line 48
            echo "    </ul>


    ";
        } elseif ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_PRO")) {
            // line 52
            echo "
    <ul>
        <li id=\"id_welcome\" class=\"menu_depliant\">
            <a class=\"menu-item\" href=\"";
            // line 55
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_welcome");
            echo "\"> Accueil </a>
        </li>
        <li>    
            <a class=\"menu-item\" href=\"";
            // line 58
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Situation </a>

        </li>
        <li>
            <a class=\"menu-item\" href=\"#\"> Opérations </a>
             <div class=\"menu-content\">
                <ul class=\"menu_list\">
                    <li><a class=\"menu-subitem\" href=\"";
            // line 65
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "transaction"));
            echo "\"> Virements </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 66
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "conversion"));
            echo "\"> Conversion </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 67
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "reconversion"));
            echo "\"> Reconversion </a></li>
                </ul>
            </div>
           
        </li>
        <li><a class=\"menu-item\" href=\"";
            // line 72
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Profil </a></li>
        <li><a class=\"menu-item\" href=\"#\"> Etendre son réseau </a></li>
    </ul>
    ";
        }
        // line 76
        echo "</nav>
<div id=\"flash_messages\">
   ";
        // line 78
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "session", array()), "flashbag", array()), "all", array(), "method"));
        foreach ($context['_seq'] as $context["key"] => $context["messages"]) {
            echo "                      
          ";
            // line 79
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["messages"]);
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                echo "                                          
              <div class=\"alert alert-";
                // line 80
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "\">                                
              ";
                // line 81
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($context["message"], array(), "FOSUserBundle"), "html", null, true);
                echo "                           
              </div>                                                             
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 83
            echo "                                                           
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['messages'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 84
        echo "  
<div>
";
    }

    // line 89
    public function block_javascripts($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "CairnUserBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  210 => 89,  204 => 84,  197 => 83,  188 => 81,  184 => 80,  178 => 79,  172 => 78,  168 => 76,  161 => 72,  153 => 67,  149 => 66,  145 => 65,  135 => 58,  129 => 55,  124 => 52,  118 => 48,  112 => 46,  110 => 45,  104 => 42,  95 => 36,  91 => 35,  86 => 34,  80 => 32,  78 => 31,  68 => 24,  65 => 23,  59 => 17,  53 => 14,  48 => 11,  46 => 10,  42 => 8,  39 => 7,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle::layout.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/layout.html.twig");
    }
}
