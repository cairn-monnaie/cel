<?php

/* CairnUserCyclosBundle:Config/AccountType:users.html.twig */
class __TwigTemplate_084540530c49a2f0dc61fa3f521a81521d379e34d89916da5b07e5c7b74ff5a3 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:users.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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

    // line 6
    public function block_body($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

    <h2> ";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "name", array()), "html", null, true);
        echo " </h2>
    <div><a href=\"";
        // line 10
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_unassign");
        echo "\"> Fermer </a></div>

    <h3> Les professionnels associés </h3>
    <table>
       <tr>
            <th> Nom </th>
            <th> Solde </th>
            <th> Capacité </th>
       </tr> 
        ";
        // line 19
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["users"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 20
            echo "            <tr>
                <td> ";
            // line 21
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "display", array()), "html", null, true);
            echo " </td>
                <td> ";
            // line 22
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "balance", array()), "html", null, true);
            echo " </td>
                <td> ";
            // line 23
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "availableBalance", array()), "html", null, true);
            echo " </td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 26
        echo "
    </table>
";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:users.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  81 => 26,  72 => 23,  68 => 22,  64 => 21,  61 => 20,  57 => 19,  45 => 10,  41 => 9,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/AccountType:users.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/users.html.twig");
    }
}
