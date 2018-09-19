<?php

/* CairnUserBundle:Banking:transfer_view.html.twig */
class __TwigTemplate_43e7a6aadcdd5a6703085ec009a2e9caf3c1c8641f6b77afe99847d184e97e5c extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:transfer_view.html.twig", 3);
        $this->blocks = array(
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
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "
    <div>

         <a href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_notice_download", array("id" => twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "id", array()))), "html", null, true);
        echo "\"> Avis d'opération </a>
        <h1> Détail du virement </h1>

        <h2> Compte à débiter </h2>
            <ul>
                <li> Nom : ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "type", array()), "name", array()), "html", null, true);
        echo "</li>
                <li> ICC : ";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "id", array()), "html", null, true);
        echo "</li>
                <li> Appartient à : 
                    ";
        // line 17
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 18
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "owner", array()), "display", array()), "html", null, true);
            echo "
                    ";
        } else {
            // line 20
            echo "                        Association Le Cairn
                    ";
        }
        // line 22
        echo "                </li>
            </ul>

        <h2> Compte à créditer </h2>
            <ul>
                <li>Nom : ";
        // line 27
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "type", array()), "name", array()), "html", null, true);
        echo "</li>
                <li> ICC : ";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array()), "id", array()), "html", null, true);
        echo "</li>
                <li> Appartient à : 
                    ";
        // line 30
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 31
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array()), "owner", array()), "display", array()), "html", null, true);
            echo "
                    ";
        } else {
            // line 33
            echo "                        Association Le Cairn
                    ";
        }
        // line 35
        echo "                </li>

            </ul>

        <em> Date : ";
        // line 39
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "date", array()), "html", null, true);
        echo " </em> </br>
        <em> Montant : ";
        // line 40
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "currencyAmount", array()), "amount", array()), "html", null, true);
        echo " </em></br>
        <em> Devise : ";
        // line 41
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "currencyAmount", array()), "currency", array()), "name", array()), "html", null, true);
        echo " </em></br>

       ";
        // line 44
        echo "    </div>    
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:transfer_view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  118 => 44,  113 => 41,  109 => 40,  105 => 39,  99 => 35,  95 => 33,  89 => 31,  87 => 30,  82 => 28,  78 => 27,  71 => 22,  67 => 20,  61 => 18,  59 => 17,  54 => 15,  50 => 14,  42 => 9,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:transfer_view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/transfer_view.html.twig");
    }
}
