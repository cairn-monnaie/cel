<?php

/* CairnUserBundle:Pdf:accounts_statement.html.twig */
class __TwigTemplate_8ee4c4d4f6a195234b5b9e384ecfcb06f81faabaf6436add4d6c9634b0e7512e extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout-pdf.html.twig", "CairnUserBundle:Pdf:accounts_statement.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout-pdf.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        $context["transactions"] = twig_get_attribute($this->env, $this->source, ($context["history"] ?? null), "transactions", array());
        // line 8
        echo "    ";
        $context["status"] = twig_get_attribute($this->env, $this->source, ($context["history"] ?? null), "status", array());
        // line 9
        echo "    ";
        $context["currency"] = twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "currency", array());
        // line 10
        echo "    ";
        $context["balance"] = twig_get_attribute($this->env, $this->source, ($context["status"] ?? null), "balanceAtBegin", array());
        // line 11
        echo "
    <table>
        <caption> Situation de votre compte ";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "type", array()), "name", array()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "owner", array()), "display", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["currency"] ?? null), "name", array()), "html", null, true);
        echo ") au ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["period"] ?? null), "end", array()), "html", null, true);
        echo "         </br>             RIB Cairn : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "id", array()), "html", null, true);
        echo "
       </caption>
        <tr> Solde au ";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["period"] ?? null), "begin", array()), "html", null, true);
        echo " : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["status"] ?? null), "balanceAtBegin", array()), "html", null, true);
        echo " </tr>
        <thead>
            <tr>
                <th> Date de valeur </th>
                <th> Description </th>
                <th> Débit </th>
                <th> Crédit </th> 
                <th> Solde </th> 
    
            </tr>
        </thead>
    
        <tbody>
            ";
        // line 28
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["transactions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 29
            echo "                ";
            $context["balance"] = (($context["balance"] ?? null) + twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()));
            // line 30
            echo "                <tr>
                    <td> ";
            // line 31
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td> ";
            // line 32
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>
                    <td> 
                        ";
            // line 34
            if ((twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()) < 0)) {
                // line 35
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
                echo " ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["currency"] ?? null), "suffix", array()), "html", null, true);
                echo "
                        ";
            }
            // line 37
            echo "                    </td>
                    <td> 
                        ";
            // line 39
            if ((twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()) > 0)) {
                // line 40
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
                echo " ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["currency"] ?? null), "suffix", array()), "html", null, true);
                echo "
                        ";
            }
            // line 42
            echo "                    </td>

                    <td> 
                        ";
            // line 45
            echo twig_escape_filter($this->env, ($context["balance"] ?? null), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["currency"] ?? null), "suffix", array()), "html", null, true);
            echo "
                    </td>

                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 50
        echo "        </tbody>

        <tr> Solde au ";
        // line 52
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["period"] ?? null), "end", array()), "html", null, true);
        echo " : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["status"] ?? null), "balanceAtEnd", array()), "html", null, true);
        echo " </tr>

    </table>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pdf:accounts_statement.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  147 => 52,  143 => 50,  130 => 45,  125 => 42,  117 => 40,  115 => 39,  111 => 37,  103 => 35,  101 => 34,  96 => 32,  92 => 31,  89 => 30,  86 => 29,  82 => 28,  64 => 15,  51 => 13,  47 => 11,  44 => 10,  41 => 9,  38 => 8,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pdf:accounts_statement.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/accounts_statement.html.twig");
    }
}
