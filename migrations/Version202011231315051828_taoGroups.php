<?php

declare(strict_types=1);

namespace oat\taoGroups\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoGroups\models\GroupsService;
use oat\taoTestTaker\models\TestTakerFormService;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202011231315051828_taoGroups extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Register additional form on test takers form';
    }

    public function up(Schema $schema): void
    {
        $testTakerFormService = $this->getServiceLocator()->get(TestTakerFormService::SERVICE_ID);
        $options = $testTakerFormService->getOptions();
        $options[TestTakerFormService::OPTION_ADDITIONAL_FORM_PROPERTIES][] = GroupsService::PROPERTY_MEMBERS_URI;
        $testTakerFormService->setOptions($options);
        $this->registerService(TestTakerFormService::SERVICE_ID, $testTakerFormService);
    }

    public function down(Schema $schema): void
    {
        $testTakerFormService = $this->getServiceLocator()->get(TestTakerFormService::SERVICE_ID);
        $options = $testTakerFormService->getOptions();
        $options = array_diff($options, [GroupsService::PROPERTY_MEMBERS_URI]);
        $options[TestTakerFormService::OPTION_ADDITIONAL_FORM_PROPERTIES][] = GroupsService::PROPERTY_MEMBERS_URI;
        $testTakerFormService->setOptions($options);
        $this->registerService(TestTakerFormService::SERVICE_ID, $testTakerFormService);
    }
}
