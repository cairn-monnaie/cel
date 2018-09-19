<?php

/* CairnUserBundle:Banknote:search.html.twig */
class __TwigTemplate_0036ea1a5e6e825f58cc024a101cfe38be568f6d76c9e9ef55dbc39adb726808 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:Banknote:search.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 6
        echo "
<h3>Recherche dynamique de billet</h3>

<div class=\"well\">
 ";
        // line 10
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "

        <div class=\"row\">
            <div class=\"col s12\">
                <div class=\"row\">
                    <div class=\"input-field col m6 s12\">
                        <i class=\"material-icons prefix\">Number</i>
                        ";
        // line 17
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "number", array()), 'widget');
        echo "
                        ";
        // line 18
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "number", array()), 'label');
        echo "
                    </div>
                </div>
            </div>
        </div>
    ";
        // line 23
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
</div>
";
    }

    // line 26
    public function block_javascripts($context, array $blocks = array())
    {
        // line 27
        echo "    <script type=\"text/javascript\">
        jQuery(function (\$) {
            ('input[name=\"form[number]\"]').autocomplete({
                data: {
            ";
        // line 31
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["banknotes"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["banknote"]) {
            // line 32
            echo "                    \"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["banknote"], "number", array()), "html", null, true);
            echo "\" 
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['banknote'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 34
        echo "                },
                limit: 10, // The max amount of results that can be shown at once. Default: Infinity.
                onAutocomplete: function(val) {
                    // Callback function when value is autcompleted.
                },
                minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
            });
        })
    </script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banknote:search.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 34,  84 => 32,  80 => 31,  74 => 27,  71 => 26,  64 => 23,  56 => 18,  52 => 17,  42 => 10,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banknote:search.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banknote/search.html.twig");
    }
}
