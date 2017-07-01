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
     * @dataProvider extractorsDataProvider
     */
    public function itShouldDefineExtractorsConfiguration($inputConfig, $expectedConfig): void
    {
        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedConfig,
            'extractor'
        );
    }

    /**
     * @test
     * @dataProvider commandBusProvider
     */
    public function itShouldDefineCommandBusConfiguration($inputConfig, $expectedConfig): void
    {
        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedConfig,
            'command_bus'
        );
    }

    /**
     * @test
     * @dataProvider listenersDataProvider
     */
    public function itShouldDefineListenersConfiguration($inputConfig, $expectedConfig): void
    {
        $this->assertProcessedConfigurationEquals(
            [$inputConfig],
            $expectedConfig,
            'listeners'
        );
    }

    public function extractorsDataProvider(): array
    {
        return [
            'default' => [
                'input' => [],
                'expected' => [
                    'extractor' => [
                        'service_id' => 'eps.req2cmd.extractor.serializer'
                    ]
                ]
            ],
            'change_builtin-extractor' => [
                'input' => [
                    'extractor' => 'jms_serializer'
                ],
                'expected' => [
                    'extractor' => [
                        'service_id' => 'eps.req2cmd.extractor.jms_serializer'
                    ]
                ]
            ],
            'own_extractor' => [
                'input' => [
                    'extractor' => [
                        'service_id' => 'app.my_extractor'
                    ]
                ],
                'expected' => [
                    'extractor' => [
                        'service_id' => 'app.my_extractor'
                    ]
                ]
            ]
        ];
    }

    public function commandBusProvider(): array
    {
        return [
            'default' => [
                'input' => [],
                'expected' => [
                    'command_bus' => [
                        'service_id' => 'eps.req2cmd.command_bus.tactician',
                        'name' => 'default'
                    ]
                ]
            ],
            'change_builtin_bus' => [
                'input' => [
                    'command_bus' => 'broadway'
                ],
                'expected' => [
                    'command_bus' => [
                        'service_id' => 'eps.req2cmd.command_bus.broadway'
                    ]
                ]
            ],
            'custom_bus' => [
                'input' => [
                    'command_bus' => [
                        'service_id' => 'app.command_bus.custom'
                    ]
                ],
                'expected' => [
                    'command_bus' => [
                        'service_id' => 'app.command_bus.custom'
                    ]
                ]
            ],
            'use_other_tactician_bus_name' => [
                'input' => [
                    'command_bus' => [
                        'name' => 'queued'
                    ]
                ],
                'expected' => [
                    'command_bus' => [
                        'service_id' => 'eps.req2cmd.command_bus.tactician',
                        'name' => 'queued'
                    ]
                ]
            ],
            'unset_name_if_not_tactician' => [
                'input' => [
                    'command_bus' => [
                        'service_id' => 'my.custom.bus',
                        'name' => 'blablabla'
                    ]
                ],
                'expected' => [
                    'command_bus' => [
                        'service_id' => 'my.custom.bus'
                    ]
                ]
            ]
        ];
    }

    public function listenersDataProvider(): array
    {
        return [
            'default' => [
                'input' => [],
                'expected' => [
                    'listeners' => [
                        'extractor' => [
                            'enabled' => true,
                            'priority' => 0
                        ]
                    ]
                ]
            ],
            'change_priority' => [
                'input' => [
                    'listeners' => [
                        'extractor' => [
                            'priority' => 128
                        ]
                    ]
                ],
                'expected' => [
                    'listeners' => [
                        'extractor' => [
                            'enabled' => true,
                            'priority' => 128
                        ]
                    ]
                ]
            ],
            'disable_listener' => [
                'input' => [
                    'listeners' => [
                        'extractor' => [
                            'enabled' => false
                        ]
                    ]
                ],
                'expected' => [
                    'listeners' => [
                        'extractor' => [
                            'enabled' => false,
                            'priority' => 0
                        ]
                    ]
                ]
            ],
            'shorthand_disabling' => [
                'input' => [
                    'listeners' => [
                        'extractor' => false
                    ]
                ],
                'expected' => [
                    'listeners' => [
                        'extractor' => [
                            'enabled' => false,
                            'priority' => 0
                        ]
                    ]
                ]
            ]
        ];
    }
}
