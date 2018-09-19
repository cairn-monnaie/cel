<?php

/* CairnUserBundle:Banking:account_operations.html.twig */
class __TwigTemplate_339739e51d5413dde1c87bf077b6b6088ec5076653aa486bc9da8f41aff18bd5 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:account_operations.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:account_operations.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:account_operations.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

    ";
        // line 8
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:account_download_options.html.twig", array("account" => (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 8, $this->source); })())));
        echo "

    <div class=\"well\">
        ";
        // line 11
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new Twig_Error_Runtime('Variable "form" does not exist.', 11, $this->source); })()), 'form_start');
        echo "
        ";
        // line 12
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new Twig_Error_Runtime('Variable "form" does not exist.', 12, $this->source); })()), 'rest');
        echo "
        ";
        // line 13
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new Twig_Error_Runtime('Variable "form" does not exist.', 13, $this->source); })()), 'form_end');
        echo "
    </div>
<div>
    <table>
    <caption> <span> Solde au ";
        // line 17
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d-m-Y"), "html", null, true);
        echo " : </span> <span> ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 17, $this->source); })()), "status", array()), "balance", array()), "html", null, true);
        echo " cairns </span> </br>
              <span> Dont opérations à venir : ";
        // line 18
        echo twig_escape_filter($this->env, (isset($context["futureAmount"]) || array_key_exists("futureAmount", $context) ? $context["futureAmount"] : (function () { throw new Twig_Error_Runtime('Variable "futureAmount" does not exist.', 18, $this->source); })()), "html", null, true);
        echo " cairns </span>

     </caption>
    
    <thead>
        <tr>
            <th> Date </th>
            <th> Opération </th> 
            <th> Montant </th>
            <th> Action </th>
  
        </tr>
    </thead>

    <tbody>
    ";
        // line 33
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["transactions"]) || array_key_exists("transactions", $context) ? $context["transactions"] : (function () { throw new Twig_Error_Runtime('Variable "transactions" does not exist.', 33, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 34
            echo "        <tr>
            <td>";
            // line 35
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo "</td>
            <td> ";
            // line 36
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td> 
            <td> ";
            // line 37
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </td>
            <td></td>
        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 41
        echo "    </tbody>
    </table>
</div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:account_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  129 => 41,  119 => 37,  115 => 36,  111 => 35,  108 => 34,  104 => 33,  86 => 18,  80 => 17,  73 => 13,  69 => 12,  65 => 11,  59 => 8,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/account_operations.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    {{include(\"CairnUserBundle:Banking:account_download_options.html.twig\",{'account':account})}}

    <div class=\"well\">
        {{ form_start(form) }}
        {{ form_rest(form) }}
        {{form_end(form) }}
    </div>
<div>
    <table>
    <caption> <span> Solde au {{ \"now\"|date(\"d-m-Y\") }} : </span> <span> {{account.status.balance}} cairns </span> </br>
              <span> Dont opérations à venir : {{futureAmount}} cairns </span>

     </caption>
    
    <thead>
        <tr>
            <th> Date </th>
            <th> Opération </th> 
            <th> Montant </th>
            <th> Action </th>
  
        </tr>
    </thead>

    <tbody>
    {% for transaction in transactions %}
        <tr>
            <td>{{ transaction.date}}</td>
            <td> {{transaction.description }} </td> 
            <td> {{transaction.amount }} </td>
            <td></td>
        </tr>
    {% endfor %}
    </tbody>
    </table>
</div>
{% endblock %}


", "CairnUserBundle:Banking:account_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/account_operations.html.twig");
    }
}
