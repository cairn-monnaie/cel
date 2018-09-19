<?php

/* CairnUserBundle:User:index.html.twig */
class __TwigTemplate_155daf037cf45cc5ebf5b1aee2bb86025d7f241909ba1c928c8a25b7feeef023 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:User:index.html.twig", 3);
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
 <h2> Espace Professionnel ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "name", array()), "html", null, true);
        echo "</h2>                                                  
<h3> ";
        // line 15
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d - m -Y"), "html", null, true);
        echo " </h3>                                                                               
<p>                                                                          

<div id=\"body_wrapper\">
    <div id=\"situation\">
        <a href=\"";
        // line 20
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "id", array()))), "html", null, true);
        echo "\">
            <span role=\"heading\" id=\"situation_title\"> situation </span>
        </a>
        <ul>
            ";
        // line 24
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["accounts"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
            // line 25
            echo "            <li class=\"accounts_info\">       
                <a href=\"";
            // line 26
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_account_operations", array("accountID" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
            echo "\">
                    <div>
                        ";
            // line 28
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "name", array()), "html", null, true);
            echo " </br>
                        ";
            // line 29
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "id", array()), "html", null, true);
            echo "
                    </div>
                    <span> ";
            // line 31
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "balance", array()), "html", null, true);
            echo " </span>
                </a>
            </li>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 35
        echo "        </ul>
    </div>
    <div id=\"operations\">
        <a href=\"#\">
            <span role=\"heading\" id=\"operations_title\"> Dernières opérations </span>
            <ul>
                ";
        // line 41
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["lastTransactions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 42
            echo "                <li class=\"transactions_info\">       
                    <div>
                        ";
            // line 44
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </br>
                        ";
            // line 45
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo "
                    </div>
                    <span> ";
            // line 47
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </span>
                </li>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 50
        echo "            </ul>
        </a>
    </div>
    <div id=\"network\">
        <img src=\"";
        // line 54
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-network.png"), "html", null, true);
        echo "\" alt=\"Network\">
        <span role=heading\" id=\"last_users_title\"> Ils ont rejoint votre réseau </span>
        <ul>
            ";
        // line 57
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["lastUsers"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 58
            echo "            <li class=\"users_show\">       
                <div>
                    <a href=\"";
            // line 60
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, $context["user"], "id", array()))), "html", null, true);
            echo "\">
                        ";
            // line 61
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "name", array()), "html", null, true);
            echo " </br>
                        ";
            // line 63
            echo "                    </a>
                </div>
            </li>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 67
        echo "        </ul>

    </div>

</div>

</p>                                                                         
                                                                            
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:User:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  183 => 67,  174 => 63,  170 => 61,  166 => 60,  162 => 58,  158 => 57,  152 => 54,  146 => 50,  137 => 47,  132 => 45,  128 => 44,  124 => 42,  120 => 41,  112 => 35,  102 => 31,  97 => 29,  93 => 28,  88 => 26,  85 => 25,  81 => 24,  74 => 20,  66 => 15,  62 => 14,  57 => 13,  54 => 12,  47 => 9,  42 => 8,  39 => 7,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:User:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/User/index.html.twig");
    }
}
