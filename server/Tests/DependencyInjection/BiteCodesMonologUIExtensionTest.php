<?php

namespace BiteCodes\MonologUIBundle\Tests\DependencyInjection;

use BiteCodes\MonologUIBundle\DependencyInjection\BiteCodesMonologUIExtension;

class BiteCodesMonologUIExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected function getContainerExtensions()
    {
        return [
            new BiteCodesMonologUIExtension(),
        ];
    }

//    /** @test */
//    public function after_loading_the_correct_parameter_has_been_set()
//    {
//        $this->load();
//
//        $this->assertContainerBuilderHasParameter('bitecodes_monolog_ui.log_config', 'some value');
//    }
}
