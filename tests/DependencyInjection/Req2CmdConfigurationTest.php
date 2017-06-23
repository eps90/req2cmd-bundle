<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\DependencyInjection;

use Eps\Req2CmdBundle\DependencyInjection\Req2CmdConfiguration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Req2CmdConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new Req2CmdConfiguration();
    }

    /**
     * @test
     */
    public function itShouldSetDefaultExtractorConfig(): void
    {
        $inputConfig = [];
        $expectedProcessedConfig = [
            'extractor' => [
                'service_id' => 'eps.req2cmd.extractor.serializer'
            ]
        ];
        $this->assertProcessedConfigurationEquals(
            [
                $inputConfig
            ],
            $expectedProcessedConfig,
            'extractor'
        );
    }

    /**
     * @test
     */
    public function itShouldBeAbleToChangeExtractorConfigToBuiltIn(): void
    {
        $inputConfig = [
            'extractor' => 'jms_serializer'
        ];
        $expectedProcessedConfig = [
            'extractor' => [
                'service_id' => 'eps.req2cmd.extractor.jms_serializer'
            ]
        ];
        $this->assertProcessedConfigurationEquals(
            [
                $inputConfig
            ],
            $expectedProcessedConfig,
            'extractor'
        );
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDefineOwnServiceForExtractor(): void
    {
        $inputConfig = [
            'extractor' => [
                'service_id' => 'app.my_extractor'
            ]
        ];
        $expectedProcessedConfig = [
            'extractor' => [
                'service_id' => 'app.my_extractor'
            ]
        ];
        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedProcessedConfig,
            'extractor'
        );
    }

    /**
     * @test
     */
    public function itShouldSetTacticianAsDefaultCommandBus(): void
    {
        $inputConfig = [];
        $expectedProcessedConfig = [
            'command_bus' => [
                'service_id' => 'eps.req2cmd.command_bus.tactician',
                'name' => 'default'
            ]
        ];
        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedProcessedConfig,
            'command_bus'
        );
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSetOtherBuiltInCommandBus(): void
    {
        $inputConfig = [
            'command_bus' => 'broadway'
        ];
        $expectedProcessedConfig = [
            'command_bus' => [
                'service_id' => 'eps.req2cmd.command_bus.broadway'
            ]
        ];
        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedProcessedConfig,
            'command_bus'
        );
    }

    /**
     * @test
     */
    public function itShouldAllowToAddCustomCommandBus(): void
    {
        $inputConfig = [
            'command_bus' => [
                'service_id' => 'app.command_bus.custom'
            ]
        ];
        $expectedProcessedConfig = [
            'command_bus' => [
                'service_id' => 'app.command_bus.custom'
            ]
        ];

        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedProcessedConfig,
            'command_bus'
        );
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSetTacticianCommandBusType(): void
    {
        $inputConfig = [
            'command_bus' => [
                'name' => 'queued'
            ]
        ];
        $expectedProcessedConfig = [
            'command_bus' => [
                'service_id' => 'eps.req2cmd.command_bus.tactician',
                'name' => 'queued'
            ]
        ];

        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedProcessedConfig,
            'command_bus'
        );
    }

    /**
     * @test
     */
    public function itShouldUnsetCommandBusNameWhenItIsNotTactician(): void
    {
        $inputConfig = [
            'command_bus' => [
                'service_id' => 'my.custom.bus',
                'name' => 'blablabla'
            ]
        ];
        $expectedProcessedConfig = [
            'command_bus' => [
                'service_id' => 'my.custom.bus'
            ]
        ];

        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedProcessedConfig,
            'command_bus'
        );
    }
}
