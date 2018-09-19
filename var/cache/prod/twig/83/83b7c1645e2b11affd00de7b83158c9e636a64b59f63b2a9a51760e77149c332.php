<?php

/* CairnUserCyclosBundle:Config/TransferFee:complete.html.twig */
class __TwigTemplate_eafdc21c7494d684daddf252d6abe7ddb880904d1075e93b18510bbbd962f80e extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/TransferFee:complete.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
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
<h3>Ajout d'un type de transfert</h3>

<div class=\"well\">

";
        // line 11
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "
    ";
        // line 12
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "toNature", array()), 'row');
        echo "  
    ";
        // line 13
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "toName", array()), 'row');
        echo " ";
        // line 14
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "

</div>

<script>
var \$toNature = \$('#form_toNature');
// When beneficiary nature gets selected ...
\$toNature.change(function() {
  // ... retrieve the corresponding form.
  var \$form = \$(this).closest('form');
  // Simulate form data, but only include the selected \$toNature value.
  var data = {};
  data[\$toNature.attr('name')] = \$toNature.val();
  // Submit data via AJAX to the form's action path.
  \$.ajax({
    url : \$form.attr('action'),
    type: \$form.attr('method'),
    data : data,
    success: function(html) {
      // Replace current position field ...
      \$('#form_toName').replaceWith(
        // ... with the returned one from the AJAX response.
        \$(html).find('#form_toName')
      );
      // Position field now displays the appropriate positions.
    }
  });
});
</script>))))
";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/TransferFee:complete.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 14,  50 => 13,  46 => 12,  42 => 11,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/TransferFee:complete.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/TransferFee/complete.html.twig");
    }
}
