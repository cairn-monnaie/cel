<?php

/* CairnUserBundle:Pdf:card.html.twig */
class __TwigTemplate_f92abaeeee83f7df44b17ebd4c0f8a7b5f8db69b72227417d4dc4da190b9a0db extends Twig_Template
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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        $context["rows"] = twig_length_filter($this->env, ($context["fields"] ?? null));
        // line 8
        echo "    ";
        $context["cols"] = twig_length_filter($this->env, ($context["fields"] ?? null));
        // line 9
        echo "    <table>
        <caption> <strong> Carte de sécurité Cairn n° ";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["card"] ?? null), "number", array()), "html", null, true);
        echo " </strong> </br>
                    <em> Propriétaire : ";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["card"] ?? null), "user", array()), "name", array()), "html", null, true);
        echo " </em>
        </caption>

        <thead>
           <tr> 
            <th></th>
            ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(1, ($context["cols"] ?? null)));
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
        $context['_seq'] = twig_ensure_traversable(range(1, ($context["rows"] ?? null)));
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
            $context['_seq'] = twig_ensure_traversable(range(1, ($context["cols"] ?? null)));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 27
                echo "                        <td>";
                echo twig_escape_filter($this->env, (($__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5 = (($__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a = ($context["fields"] ?? null)) && is_array($__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a) || $__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a instanceof ArrayAccess ? ($__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a[($context["letter"] - 1)] ?? null) : null)) && is_array($__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5) || $__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5 instanceof ArrayAccess ? ($__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5[($context["i"] - 1)] ?? null) : null), "html", null, true);
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
        return array (  106 => 31,  99 => 29,  90 => 27,  86 => 26,  82 => 25,  79 => 24,  75 => 23,  70 => 20,  61 => 18,  57 => 17,  48 => 11,  44 => 10,  41 => 9,  38 => 8,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pdf:card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/card.html.twig");
    }
}
