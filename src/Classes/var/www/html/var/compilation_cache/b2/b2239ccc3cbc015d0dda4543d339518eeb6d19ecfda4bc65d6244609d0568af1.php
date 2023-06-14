<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* default.html */
class __TwigTemplate_35af8556083280ada67192a27b82fe901341b60f59be08aaaccb0b1a126b1efd extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<h1>Abfahrten Mollwitzstr ab <?php echo \$currentTime?></h1>
<div class=\"table pull-left\">
test
    ";
        // line 4
        echo twig_escape_filter($this->env, ($context["the"] ?? null), "html", null, true);
        echo "
    <?php
                \$departureController->view();
    \$departureController->check();
    ?>
</div>
";
    }

    public function getTemplateName()
    {
        return "default.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "default.html", "/var/www/html/packages/departures/resources/templates/default.html");
    }
}
