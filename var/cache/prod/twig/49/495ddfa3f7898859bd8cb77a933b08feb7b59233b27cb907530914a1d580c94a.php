<?php

/* CairnUserBundle:Banking:accounts_table.html.twig */
class __TwigTemplate_89111bd50337084329408d39792172cf7351ea2265cd736a85e23641b0d15f18 extends Twig_Template
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
    <caption>  Les comptes  </caption>
    <thead>
        <tr>
            <th> Nom du compte </th>
            <th> Identifiant </th>
            <th> Solde </th>
            <th> Capacité de dépense </th> 
            <th> Action </th> 

        </tr>
    </thead>

    <tbody>
        ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["accounts"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
            // line 18
            echo "            <tr>
                <td> ";
            // line 19
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "name", array()), "html", null, true);
            echo " </td>
                <td> ";
            // line 20
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "id", array()), "html", null, true);
            echo " </td>
                <td> ";
            // line 21
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "balance", array()), "html", null, true);
            echo " </td>
                <td> 
                    ";
            // line 23
            if (twig_get_attribute($this->env, $this->source, $context["account"], "unlimited", array())) {
                // line 24
                echo "                        infinie
                    ";
            } else {
                // line 26
                echo "                        ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "availableBalance", array()), "html", null, true);
                echo " 
                    ";
            }
            // line 28
            echo "                </td>
                <td>
                    ";
            // line 30
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 31
                echo "                        ";
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "nature", array()) == "USER")) {
                    // line 32
                    echo "                            <a href=\"";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_account_edit", array("id" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
                    echo "\"> Modifier </a>
                        ";
                }
                // line 34
                echo "                    ";
            } else {
                // line 35
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_account_operations", array("accountID" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
                echo "\"> Voir </a>
                    ";
            }
            // line 37
            echo "                </td>
            </tr>
        ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 40
            echo "            Pas de compte ouvert
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 42
        echo "    </tbody>

    </table> 
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:accounts_table.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 42,  104 => 40,  97 => 37,  91 => 35,  88 => 34,  82 => 32,  79 => 31,  77 => 30,  73 => 28,  67 => 26,  63 => 24,  61 => 23,  56 => 21,  52 => 20,  48 => 19,  45 => 18,  40 => 17,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:accounts_table.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/accounts_table.html.twig");
    }
}
