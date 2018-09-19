<?php

/* CairnUserBundle:Pdf:card.html.twig */
class __TwigTemplate_2cec84cc0856a44998e9c0e23f6676d8685bd26dc9cdda3852ad6d2fa7b20c1e extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout-pdf.html.twig", "CairnUserBundle:Pdf:card.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:card.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:card.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 6
    public function block_fos_user_content($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        // line 7
        echo "    ";
        $context["rows"] = twig_length_filter($this->env, (isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new Twig_Error_Runtime('Variable "fields" does not exist.', 7, $this->source); })()));
        // line 8
        echo "    ";
        $context["cols"] = twig_length_filter($this->env, (isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new Twig_Error_Runtime('Variable "fields" does not exist.', 8, $this->source); })()));
        // line 9
        echo "    <table>
        <caption> <strong> Carte de sécurité Cairn n° ";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["card"]) || array_key_exists("card", $context) ? $context["card"] : (function () { throw new Twig_Error_Runtime('Variable "card" does not exist.', 10, $this->source); })()), "number", array()), "html", null, true);
        echo " </strong> </br>
                    <em> Propriétaire : ";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["card"]) || array_key_exists("card", $context) ? $context["card"] : (function () { throw new Twig_Error_Runtime('Variable "card" does not exist.', 11, $this->source); })()), "user", array()), "name", array()), "html", null, true);
        echo " </em>
        </caption>

        <thead>
           <tr> 
            <th></th>
            ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(1, (isset($context["cols"]) || array_key_exists("cols", $context) ? $context["cols"] : (function () { throw new Twig_Error_Runtime('Variable "cols" does not exist.', 17, $this->source); })())));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 18
            echo "                <th><strong> ";
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "</strong></th>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 20
        echo "           </tr> 
        </thead>
        <tbody>
            ";
        // line 23
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(1, (isset($context["rows"]) || array_key_exists("rows", $context) ? $context["rows"] : (function () { throw new Twig_Error_Runtime('Variable "rows" does not exist.', 23, $this->source); })())));
        foreach ($context['_seq'] as $context["_key"] => $context["letter"]) {
            // line 24
            echo "                <tr>
                    <td> <strong> &#";
            // line 25
            echo twig_escape_filter($this->env, (64 + $context["letter"]), "html", null, true);
            echo " </strong> </td>
                    ";
            // line 26
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(range(1, (isset($context["cols"]) || array_key_exists("cols", $context) ? $context["cols"] : (function () { throw new Twig_Error_Runtime('Variable "cols" does not exist.', 26, $this->source); })())));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 27
                echo "                        <td>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new Twig_Error_Runtime('Variable "fields" does not exist.', 27, $this->source); })()), ($context["letter"] - 1), array(), "array"), ($context["i"] - 1), array(), "array"), "html", null, true);
                echo "</td>        
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 29
            echo "                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['letter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo "        </tbody>
    </table>

    Cette carte de sécurité vous permet de réaliser les opérations considérées comme sensibles sur la plateforme de paiements du Cairn. Ne la transmettez sous aucun prétexte. En cas de perte ou de vol, révoquez-là immédiatement puis commandez-en une nouvelle.
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pdf:card.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 31,  117 => 29,  108 => 27,  104 => 26,  100 => 25,  97 => 24,  93 => 23,  88 => 20,  79 => 18,  75 => 17,  66 => 11,  62 => 10,  59 => 9,  56 => 8,  53 => 7,  44 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Pdf/card.html.twig #}         

{% extends 'CairnUserBundle::layout-pdf.html.twig' %}


{% block fos_user_content %}
    {% set rows = fields|length %}
    {% set cols = fields|length %}
    <table>
        <caption> <strong> Carte de sécurité Cairn n° {{card.number}} </strong> </br>
                    <em> Propriétaire : {{card.user.name }} </em>
        </caption>

        <thead>
           <tr> 
            <th></th>
            {% for i in 1..cols %}
                <th><strong> {{i}}</strong></th>
            {% endfor %}
           </tr> 
        </thead>
        <tbody>
            {% for letter in 1..rows %}
                <tr>
                    <td> <strong> &#{{64+letter}} </strong> </td>
                    {% for i in 1..cols%}
                        <td>{{fields[letter-1][i-1]}}</td>        
                    {% endfor %}
                </tr>
            {% endfor %}
        </tbody>
    </table>

    Cette carte de sécurité vous permet de réaliser les opérations considérées comme sensibles sur la plateforme de paiements du Cairn. Ne la transmettez sous aucun prétexte. En cas de perte ou de vol, révoquez-là immédiatement puis commandez-en une nouvelle.
{% endblock %}
", "CairnUserBundle:Pdf:card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/card.html.twig");
    }
}
