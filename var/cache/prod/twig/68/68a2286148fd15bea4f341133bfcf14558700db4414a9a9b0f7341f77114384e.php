<?php

/* CairnUserBundle:Pro:add_user.html.twig */
class __TwigTemplate_e651f52d7f35c8e97819c814f9a5dbea6844fb5dd15c3efa51ed0000eabc30cc extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Pro:add_user.html.twig", 3);
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
<div>                                                                          
        ";
        // line 15
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "            
        ";
        // line 16
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

          var zipCities = ";
        // line 29
        echo json_encode(($context["zipCities"] ?? null));
        echo ";

          var list = new Array();
        
          \$containerCity =  \$(\"#app_user_registration_address_zipCity_city\");
          \$containerZipCode =  \$(\"#app_user_registration_address_zipCity_zipCode\");

          for (var i = 0, c = zipCities.length; i < c ; i++){
                list.push({label : zipCities[i].zipCode + \" , \" +  zipCities[i].city, value: zipCities[i].city }); 

         }
            
            \$(\"#app_user_registration_address_zipCity_city, #app_user_registration_address_zipCity_zipCode\").autocomplete({
                source: function(request,response){
                            if(\$(this).attr('id') == \$containerCity.attr('id')){
                                response(\$.map(zipCities, function(object)
                                {
                                    return {
                                            label: object.zipCode + \", \" + object.city,
                                            value: function()
                                            {
                                                    \$containerCity.val(object.city);
                                                    return object.zipCode;
                                            }

                                    }
                                }));
                            }
                            else{
                                response(\$.map(zipCities, function(object)
                                {
                                    return {
                                            label: object.zipCode + \", \" + object.city,
                                            value: function()
                                            {
                                                    \$containerZipCode.val(object.zipCode);
                                                    return object.city;
                                            }
                                    }
                                }));

                            }
                        },

                minLength : 1,
                delay: 600,
                select: function(event,ui){
                    if(\$(this).attr('id') == \$containerCity.attr('id')){
                        \$containerZipCode.val(ui.item.value);
                    }
                    else{
                        \$containerCity.val(ui.item.value);
                    }

                },

            });
        });

    </script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pro:add_user.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 29,  80 => 23,  77 => 22,  68 => 16,  64 => 15,  58 => 13,  55 => 12,  48 => 9,  43 => 8,  40 => 7,  35 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pro:add_user.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/add_user.html.twig");
    }
}
