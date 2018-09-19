<?php

/* CairnUserBundle:Pdf:operation_notice.html.twig */
class __TwigTemplate_811aaaabf319baee6ad4ec43d991deca14e9264e214df238d71a7287dfb21e43 extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:operation_notice.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:operation_notice.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 17, $this->source); })()), "transactionNumber", array()), "html", null, true);
        echo ", enregistré le ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 17, $this->source); })()), "date", array()), "html", null, true);
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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 33, $this->source); })()), "currencyAmount", array()), "amount", array()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 33, $this->source); })()), "currencyAmount", array()), "currency", array()), "suffix", array()), "html", null, true);
        echo " </td> 
            <td> ";
        // line 34
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 34, $this->source); })()), "date", array()), "html", null, true);
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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 50, $this->source); })()), "from", array()), "type", array()), "name", array()), "html", null, true);
        echo " </br>
                 ";
        // line 51
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 51, $this->source); })()), "from", array()), "id", array()), "html", null, true);
        echo " </br>
                 ";
        // line 52
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 53
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 53, $this->source); })()), "from", array()), "owner", array()), "display", array()), "html", null, true);
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
        echo twig_escape_filter($this->env, (isset($context["description"]) || array_key_exists("description", $context) ? $context["description"] : (function () { throw new Twig_Error_Runtime('Variable "description" does not exist.', 58, $this->source); })()), "html", null, true);
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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 74, $this->source); })()), "to", array()), "type", array()), "name", array()), "html", null, true);
        echo " </br>
                 ";
        // line 75
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 75, $this->source); })()), "to", array()), "id", array()), "html", null, true);
        echo " </br>
                 ";
        // line 76
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 77
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 77, $this->source); })()), "to", array()), "owner", array()), "display", array()), "html", null, true);
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
        echo twig_escape_filter($this->env, (isset($context["description"]) || array_key_exists("description", $context) ? $context["description"] : (function () { throw new Twig_Error_Runtime('Variable "description" does not exist.', 82, $this->source); })()), "html", null, true);
        echo " </td>
        </tbody>
    </table> 

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

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
        return array (  183 => 82,  180 => 81,  176 => 79,  170 => 77,  168 => 76,  164 => 75,  160 => 74,  141 => 58,  138 => 57,  134 => 55,  128 => 53,  126 => 52,  122 => 51,  118 => 50,  99 => 34,  93 => 33,  75 => 18,  69 => 17,  59 => 10,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Pdf/operation_notice.html.twig #}         

{% extends 'CairnUserBundle::layout-pdf.html.twig' %}

{% block fos_user_content %}
    <p>This is the PDF content.</p>


    <figure>
        <img src=\"{{ asset('purple-unknown.png') }}\" alt=\"Logo Cairn\">
        <figcaption>A picture with an absolute URL</figcaption>
    </figure>

   <table>
        <tbody>
           <td> <strong> Avis d'opération de virement </strong> </td> 
           <td> Compte-rendu de votre virement, d'ordre n°{{transfer.transactionNumber}}, enregistré le {{transfer.date}} </td>
           <td> Imprimé le {{ 'now'|date('d - m -Y') }} <td>
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
            <td> {{transfer.currencyAmount.amount}} {{transfer.currencyAmount.currency.suffix}} </td> 
            <td> {{transfer.date}} </td>
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
            <td> {{transfer.from.type.name}} </br>
                 {{transfer.from.id}} </br>
                 {% if transfer.from.owner.display is defined %}
                        {{transfer.from.owner.display}}
                 {% else %}
                        Association Le Cairn
                 {% endif %}
             </td> 
            <td> {{description}} </td>
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
            <td> {{transfer.to.type.name}} </br>
                 {{transfer.to.id}} </br>
                 {% if transfer.to.owner.display is defined %}
                        {{transfer.to.owner.display}}
                 {% else %}
                        Association Le Cairn
                 {% endif %}
             </td> 
            <td> {{description}} </td>
        </tbody>
    </table> 

{% endblock %}


", "CairnUserBundle:Pdf:operation_notice.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/operation_notice.html.twig");
    }
}
