<?php

/* bootstrap_3_horizontal_layout.html.twig */
class __TwigTemplate_b81fa8f95f78523cae2ee8c98ebe6589196d5dea7b801e83b169bdf1838373e9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $_trait_0 = $this->loadTemplate("bootstrap_3_layout.html.twig", "bootstrap_3_horizontal_layout.html.twig", 1);
        // line 1
        if (!$_trait_0->isTraitable()) {
            throw new Twig_Error_Runtime('Template "'."bootstrap_3_layout.html.twig".'" cannot be used as a trait.', 1, $this->source);
        }
        $_trait_0_blocks = $_trait_0->getBlocks();

        $this->traits = $_trait_0_blocks;

        $this->blocks = array_merge(
            $this->traits,
            array(
                'form_start' => array($this, 'block_form_start'),
                'form_label' => array($this, 'block_form_label'),
                'form_label_class' => array($this, 'block_form_label_class'),
                'form_row' => array($this, 'block_form_row'),
                'checkbox_row' => array($this, 'block_checkbox_row'),
                'radio_row' => array($this, 'block_radio_row'),
                'checkbox_radio_row' => array($this, 'block_checkbox_radio_row'),
                'submit_row' => array($this, 'block_submit_row'),
                'reset_row' => array($this, 'block_reset_row'),
                'form_group_class' => array($this, 'block_form_group_class'),
            )
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "
";
        // line 3
        $this->displayBlock('form_start', $context, $blocks);
        // line 7
        echo "
";
        // line 9
        echo "
";
        // line 10
        $this->displayBlock('form_label', $context, $blocks);
        // line 20
        echo "
";
        // line 21
        $this->displayBlock('form_label_class', $context, $blocks);
        // line 24
        echo "
";
        // line 26
        echo "
";
        // line 27
        $this->displayBlock('form_row', $context, $blocks);
        // line 36
        echo "
";
        // line 37
        $this->displayBlock('checkbox_row', $context, $blocks);
        // line 40
        echo "
";
        // line 41
        $this->displayBlock('radio_row', $context, $blocks);
        // line 44
        echo "
";
        // line 45
        $this->displayBlock('checkbox_radio_row', $context, $blocks);
        // line 56
        echo "
";
        // line 57
        $this->displayBlock('submit_row', $context, $blocks);
        // line 67
        echo "
";
        // line 68
        $this->displayBlock('reset_row', $context, $blocks);
        // line 78
        echo "
";
        // line 79
        $this->displayBlock('form_group_class', $context, $blocks);
    }

    // line 3
    public function block_form_start($context, array $blocks = array())
    {
        // line 4
        $context["attr"] = twig_array_merge(($context["attr"] ?? null), array("class" => twig_trim_filter((((twig_get_attribute($this->env, $this->source, ($context["attr"] ?? null), "class", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["attr"] ?? null), "class", array()), "")) : ("")) . " form-horizontal"))));
        // line 5
        $this->displayParentBlock("form_start", $context, $blocks);
    }

    // line 10
    public function block_form_label($context, array $blocks = array())
    {
        // line 11
        ob_start();
        // line 12
        echo "    ";
        if ((($context["label"] ?? null) === false)) {
            // line 13
            echo "        <div class=\"";
            $this->displayBlock("form_label_class", $context, $blocks);
            echo "\"></div>
    ";
        } else {
            // line 15
            echo "        ";
            $context["label_attr"] = twig_array_merge(($context["label_attr"] ?? null), array("class" => twig_trim_filter(((((twig_get_attribute($this->env, $this->source, ($context["label_attr"] ?? null), "class", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["label_attr"] ?? null), "class", array()), "")) : ("")) . " ") .             $this->renderBlock("form_label_class", $context, $blocks)))));
            // line 16
            $this->displayParentBlock("form_label", $context, $blocks);
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 21
    public function block_form_label_class($context, array $blocks = array())
    {
        // line 22
        echo "col-sm-2";
    }

    // line 27
    public function block_form_row($context, array $blocks = array())
    {
        // line 28
        echo "<div class=\"form-group";
        if ((( !($context["compound"] ?? null) || (((isset($context["force_error"]) || array_key_exists("force_error", $context))) ? (_twig_default_filter(($context["force_error"] ?? null), false)) : (false))) &&  !($context["valid"] ?? null))) {
            echo " has-error";
        }
        echo "\">";
        // line 29
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'label');
        // line 30
        echo "<div class=\"";
        $this->displayBlock("form_group_class", $context, $blocks);
        echo "\">";
        // line 31
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        // line 32
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        // line 33
        echo "</div>
";
        // line 34
        echo "</div>";
    }

    // line 37
    public function block_checkbox_row($context, array $blocks = array())
    {
        // line 38
        $this->displayBlock("checkbox_radio_row", $context, $blocks);
    }

    // line 41
    public function block_radio_row($context, array $blocks = array())
    {
        // line 42
        $this->displayBlock("checkbox_radio_row", $context, $blocks);
    }

    // line 45
    public function block_checkbox_radio_row($context, array $blocks = array())
    {
        // line 46
        ob_start();
        // line 47
        echo "    <div class=\"form-group";
        if ( !($context["valid"] ?? null)) {
            echo " has-error";
        }
        echo "\">
        <div class=\"";
        // line 48
        $this->displayBlock("form_label_class", $context, $blocks);
        echo "\"></div>
        <div class=\"";
        // line 49
        $this->displayBlock("form_group_class", $context, $blocks);
        echo "\">
            ";
        // line 50
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        echo "
            ";
        // line 51
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        echo "
        </div>
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 57
    public function block_submit_row($context, array $blocks = array())
    {
        // line 58
        ob_start();
        // line 59
        echo "    <div class=\"form-group\">
        <div class=\"";
        // line 60
        $this->displayBlock("form_label_class", $context, $blocks);
        echo "\"></div>
        <div class=\"";
        // line 61
        $this->displayBlock("form_group_class", $context, $blocks);
        echo "\">
            ";
        // line 62
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        echo "
        </div>
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 68
    public function block_reset_row($context, array $blocks = array())
    {
        // line 69
        ob_start();
        // line 70
        echo "    <div class=\"form-group\">
        <div class=\"";
        // line 71
        $this->displayBlock("form_label_class", $context, $blocks);
        echo "\"></div>
        <div class=\"";
        // line 72
        $this->displayBlock("form_group_class", $context, $blocks);
        echo "\">
            ";
        // line 73
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        echo "
        </div>
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 79
    public function block_form_group_class($context, array $blocks = array())
    {
        // line 80
        echo "col-sm-10";
    }

    public function getTemplateName()
    {
        return "bootstrap_3_horizontal_layout.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  268 => 80,  265 => 79,  256 => 73,  252 => 72,  248 => 71,  245 => 70,  243 => 69,  240 => 68,  231 => 62,  227 => 61,  223 => 60,  220 => 59,  218 => 58,  215 => 57,  206 => 51,  202 => 50,  198 => 49,  194 => 48,  187 => 47,  185 => 46,  182 => 45,  178 => 42,  175 => 41,  171 => 38,  168 => 37,  164 => 34,  161 => 33,  159 => 32,  157 => 31,  153 => 30,  151 => 29,  145 => 28,  142 => 27,  138 => 22,  135 => 21,  129 => 16,  126 => 15,  120 => 13,  117 => 12,  115 => 11,  112 => 10,  108 => 5,  106 => 4,  103 => 3,  99 => 79,  96 => 78,  94 => 68,  91 => 67,  89 => 57,  86 => 56,  84 => 45,  81 => 44,  79 => 41,  76 => 40,  74 => 37,  71 => 36,  69 => 27,  66 => 26,  63 => 24,  61 => 21,  58 => 20,  56 => 10,  53 => 9,  50 => 7,  48 => 3,  45 => 2,  18 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "bootstrap_3_horizontal_layout.html.twig", "/var/www/Symfony/CairnB2B/vendor/symfony/symfony/src/Symfony/Bridge/Twig/Resources/views/Form/bootstrap_3_horizontal_layout.html.twig");
    }
}
