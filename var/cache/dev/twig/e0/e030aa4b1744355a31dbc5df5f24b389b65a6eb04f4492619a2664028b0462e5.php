<?php

/* CairnUserBundle:Banking:conversion.html.twig */
class __TwigTemplate_3056655e6a6301721a63c35dc01541e7839cfd29a815dfb7286cf8a97b3c352d extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:conversion.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:conversion.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:conversion.html.twig"));

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
        if (((isset($context["to"]) || array_key_exists("to", $context) ? $context["to"] : (function () { throw new Twig_Error_Runtime('Variable "to" does not exist.', 8, $this->source); })()) == "other")) {
            // line 9
            echo "    <div class=\"well\">
      ";
            // line 10
            echo             $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["formUser"]) || array_key_exists("formUser", $context) ? $context["formUser"] : (function () { throw new Twig_Error_Runtime('Variable "formUser" does not exist.', 10, $this->source); })()), 'form_start');
            echo "
      ";
            // line 11
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock((isset($context["formUser"]) || array_key_exists("formUser", $context) ? $context["formUser"] : (function () { throw new Twig_Error_Runtime('Variable "formUser" does not exist.', 11, $this->source); })()), 'rest');
            echo "
      ";
            // line 12
            echo             $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["formUser"]) || array_key_exists("formUser", $context) ? $context["formUser"] : (function () { throw new Twig_Error_Runtime('Variable "formUser" does not exist.', 12, $this->source); })()), 'form_end');
            echo "

    </div>
    ";
        }
        // line 16
        echo "
    <div class=\"well\">
      ";
        // line 18
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["formConversion"]) || array_key_exists("formConversion", $context) ? $context["formConversion"] : (function () { throw new Twig_Error_Runtime('Variable "formConversion" does not exist.', 18, $this->source); })()), 'form_start');
        echo "
      ";
        // line 19
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, (isset($context["formConversion"]) || array_key_exists("formConversion", $context) ? $context["formConversion"] : (function () { throw new Twig_Error_Runtime('Variable "formConversion" does not exist.', 19, $this->source); })()), "toAccount", array()), 'widget', array("attr" => array("class" => "hidden-row")));
        echo "
      ";
        // line 20
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock((isset($context["formConversion"]) || array_key_exists("formConversion", $context) ? $context["formConversion"] : (function () { throw new Twig_Error_Runtime('Variable "formConversion" does not exist.', 20, $this->source); })()), 'rest');
        echo "
      ";
        // line 21
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["formConversion"]) || array_key_exists("formConversion", $context) ? $context["formConversion"] : (function () { throw new Twig_Error_Runtime('Variable "formConversion" does not exist.', 21, $this->source); })()), 'form_end');
        echo "

    </div>

    ";
        // line 25
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_list.html.twig", array("accounts" => (isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new Twig_Error_Runtime('Variable "accounts" does not exist.', 25, $this->source); })()), "type" => "credit"));
        echo "

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 29
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        // line 30
        echo "    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            \$formAccountOwner = \$('#conversion_toAccount_owner');    
            \$formAccountId = \$('#conversion_toAccount_id');    
           
            \$containerAccount = \$('.account'); 
            \$containerAccount.click(function (e){
                \$formAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                \$formAccountId.val(\$(this).children(\"span:last\")[0].innerText);
            });
        });
    </script>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:conversion.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 30,  116 => 29,  103 => 25,  96 => 21,  92 => 20,  88 => 19,  84 => 18,  80 => 16,  73 => 12,  69 => 11,  65 => 10,  62 => 9,  60 => 8,  54 => 6,  45 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/conversion.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    {% if to == 'other' %}
    <div class=\"well\">
      {{ form_start(formUser) }}
      {{ form_rest(formUser) }}
      {{ form_end(formUser) }}

    </div>
    {% endif %}

    <div class=\"well\">
      {{ form_start(formConversion) }}
      {{ form_widget(formConversion.toAccount, {'attr': {'class':'hidden-row'} }) }}
      {{ form_rest(formConversion) }}
      {{ form_end(formConversion) }}

    </div>

    {{include(\"CairnUserBundle:Banking:accounts_list.html.twig\",{'accounts':accounts,'type':'credit'})}}

{% endblock %}

{% block javascripts %}
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            \$formAccountOwner = \$('#conversion_toAccount_owner');    
            \$formAccountId = \$('#conversion_toAccount_id');    
           
            \$containerAccount = \$('.account'); 
            \$containerAccount.click(function (e){
                \$formAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                \$formAccountId.val(\$(this).children(\"span:last\")[0].innerText);
            });
        });
    </script>
{% endblock %}
", "CairnUserBundle:Banking:conversion.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/conversion.html.twig");
    }
}
