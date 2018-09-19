<?php

/* CairnUserBundle:Pdf:operation_notice.html.twig */
class __TwigTemplate_5e4b70abd4629b845608c202dfda3bc5937a55db21176ed58e1ba986e6f133b7 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout-pdf.html.twig", "CairnUserBundle:Pdf:operation_notice.html.twig", 3);
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

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 6
        echo "    <p>This is the PDF content.</p>


    <figure>
        <img src=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
        echo "\" alt=\"Logo Cairn\">
        <figcaption>A picture with an absolute URL</figcaption>
    </figure>

   <table>
        <tbody>
           <td> <strong> Avis d'opération de virement </strong> </td> 
           <td> Compte-rendu de votre virement, d'ordre n°";
        // line 17
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "transactionNumber", array()), "html", null, true);
        echo ", enregistré le ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "date", array()), "html", null, true);
        echo " </td>
           <td> Imprimé le ";
        // line 18
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d - m -Y"), "html", null, true);
        echo " <td>
        </tbody>
    </table> 

   <table>
        <thead>
            <tr>
                <th> Montant </th>
                <th> Date d'éxecution </th>
                <th> Etat </th>
    
            </tr>
        </thead>
    
        <tbody>
            <td> ";
        // line 33
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "currencyAmount", array()), "amount", array()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "currencyAmount", array()), "currency", array()), "suffix", array()), "html", null, true);
        echo " </td> 
            <td> ";
        // line 34
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "date", array()), "html", null, true);
        echo " </td>
            <td> A FAIRE <td>
        </tbody>
    </table> 

   <table>
    <caption> Compte à débiter </caption>
        <thead>
            <tr>
                <th> Compte </th>
                <th> Description </th>
    
            </tr>
        </thead>
    
        <tbody>
            <td> ";
        // line 50
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "type", array()), "name", array()), "html", null, true);
        echo " </br>
                 ";
        // line 51
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "id", array()), "html", null, true);
        echo " </br>
                 ";
        // line 52
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 53
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array()), "owner", array()), "display", array()), "html", null, true);
            echo "
                 ";
        } else {
            // line 55
            echo "                        Association Le Cairn
                 ";
        }
        // line 57
        echo "             </td> 
            <td> ";
        // line 58
        echo twig_escape_filter($this->env, ($context["description"] ?? null), "html", null, true);
        echo " </td>
        </tbody>
    </table> 


   <table>
    <caption> Compte à créditer </caption>
        <thead>
            <tr>
                <th> Compte </th>
                <th> Description </th>
    
            </tr>
        </thead>
    
        <tbody>
            <td> ";
        // line 74
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array()), "type", array()), "name", array()), "html", null, true);
        echo " </br>
                 ";
        // line 75
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array()), "id", array()), "html", null, true);
        echo " </br>
                 ";
        // line 76
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 77
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array()), "owner", array()), "display", array()), "html", null, true);
            echo "
                 ";
        } else {
            // line 79
            echo "                        Association Le Cairn
                 ";
        }
        // line 81
        echo "             </td> 
            <td> ";
        // line 82
        echo twig_escape_filter($this->env, ($context["description"] ?? null), "html", null, true);
        echo " </td>
        </tbody>
    </table> 

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pdf:operation_notice.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  165 => 82,  162 => 81,  158 => 79,  152 => 77,  150 => 76,  146 => 75,  142 => 74,  123 => 58,  120 => 57,  116 => 55,  110 => 53,  108 => 52,  104 => 51,  100 => 50,  81 => 34,  75 => 33,  57 => 18,  51 => 17,  41 => 10,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pdf:operation_notice.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/operation_notice.html.twig");
    }
}
