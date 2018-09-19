<?php

/* CairnUserBundle:Pro:add_beneficiaries.html.twig */
class __TwigTemplate_293934add0bba661f3f08ce82bc4fad3a2fd4f7ca67f66bee8d8e3ed5fc8e56d extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Pro:add_beneficiaries.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 8
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("pro.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
";
    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo " 

    <div class=\"body_wrapper\">
        ";
        // line 16
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "                                                       
         ";
        // line 17
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'rest');
        echo "                                                        
        ";
        // line 18
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "   
    </div>
";
    }

    // line 22
    public function block_javascripts($context, array $blocks = array())
    {
        // line 23
        echo "    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            var list = new Array();
             ";
        // line 29
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["pros"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["pro"]) {
            // line 30
            echo "                list.push({  value : \"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["pro"], "name", array()), "html", null, true);
            echo "\", info :{name:\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["pro"], "name", array()), "html", null, true);
            echo "\", email :\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["pro"], "email", array()), "html", null, true);
            echo "\"}   }); 
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pro'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "            \$containerName = \$('#form_name');
            \$containerEmail = \$('#form_email');

            list_names=\$.map(list, function(object){
                return object.info;
            });

            console.log(list_names);
           \$containerName.autocomplete({
                source : list,
                minLength : 2,
                select : function(event,ui){
                    \$containerEmail.val(ui.item.info.email);
                }
            }); 
           \$containerEmail.autocomplete({
                source : list,
                data : \$.map(list, function(object){
                            return object.info.email;
                       }),
                minLength : 2,
                select : function(event,ui){
                     \$containerName.val(ui.item.info.name);
                }
            }); 

        });
    </script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pro:add_beneficiaries.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  107 => 32,  94 => 30,  90 => 29,  82 => 23,  79 => 22,  72 => 18,  68 => 17,  64 => 16,  57 => 13,  54 => 12,  48 => 9,  43 => 8,  40 => 7,  35 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pro:add_beneficiaries.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/add_beneficiaries.html.twig");
    }
}
