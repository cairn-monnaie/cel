<?php

/* CairnUserBundle:Banking:accounts_list.html.twig */
class __TwigTemplate_19dacd30d2af357f31a5cef44b90535c558140580db8a28b705cbbf5d6c1d1a9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "
<table>
    <tr>
        <td>
            <div>
            <ul>
                ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["accounts"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
            // line 9
            echo "                    ";
            if ((twig_get_attribute($this->env, $this->source, $context["account"], "unlimited", array()) == false)) {
                // line 10
                echo "                        <li class=\"account\" id = \"account_";
                echo twig_escape_filter($this->env, ($context["type"] ?? null), "html", null, true);
                echo "\"> <em class=\"account_owner\"> 
                             ";
                // line 11
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "nature", array()) == "SYSTEM")) {
                    // line 12
                    echo "                                 SYSTEM
                             ";
                } else {
                    // line 14
                    echo "                                 ";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "owner", array()), "display", array()), "html", null, true);
                    echo " 
                             ";
                }
                // line 16
                echo "                        </em> </br> 
                        <span class=\"account_type\">";
                // line 17
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "name", array()), "html", null, true);
                echo " </span> </br>
                        <span class=\"account_balance\">";
                // line 18
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "balance", array()), "html", null, true);
                echo " </span> </br>
                        <span class=\"account_id\">";
                // line 19
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "id", array()), "html", null, true);
                echo " </span></li>
                    ";
            }
            // line 21
            echo "                ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 22
            echo "                    Pas encore de compte à créditer
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        echo "            </ul>
            </div>
        </td>
    </tr>
</table>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:accounts_list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 24,  77 => 22,  72 => 21,  67 => 19,  63 => 18,  59 => 17,  56 => 16,  50 => 14,  46 => 12,  44 => 11,  39 => 10,  36 => 9,  31 => 8,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:accounts_list.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/accounts_list.html.twig");
    }
}
