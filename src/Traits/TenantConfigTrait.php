<?php

namespace FS\MultiTenancyBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\String\Slugger\SluggerInterface;
use FS\MultiTenancyBundle\Enum\DatabaseStatusEnum;
use FS\MultiTenancyBundle\Enum\FixturesStatusEnum;
use FS\MultiTenancyBundle\Enum\MigrationStatusEnum;

/**
 *  Trait to add tenant database configuration to an entity.
 * @author Ramy Hakam <pencilsoft1@gmail.com>
 */
trait TenantConfigTrait
{
    #[ORM\Column(name: "email", type: "string", length: 128)]
    #[Assert\Length(
        min: 6,
        max: 128,
        minMessage: "The username must be more than {{limit}} characters",
        maxMessage: "The username must be less than {{limit}} characters",
    )]
    public $email;

    #[ORM\Column(name: "name", type: "string", length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 128,
    )]
    public $name;

    #[ORM\Column(name: "companyName", type: "string", length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 128,
    )]
    public $companyName;

    #[ORM\Column(name: "subdomain", type: "string", length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 128,
    )]
    public $subdomain;


    #[ORM\Column(name: "dbName", type: "string", length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 128,
    )]
    public $dbName;

    #[ORM\Column(name: "dbStatus", type: 'string', length: 255, enumType: DatabaseStatusEnum::class)]
    private DatabaseStatusEnum $dbStatus = DatabaseStatusEnum::DATABASE_NOT_CREATED;

    #[ORM\Column(name: "fixturesStatus", type: 'string', length: 255, enumType: FixturesStatusEnum::class)]
    private FixturesStatusEnum $fixturesStatus = FixturesStatusEnum::FIXTURES_NOT_CREATED;

    #[ORM\Column(name: "migrationStatus", type: 'string', length: 255, enumType: MigrationStatusEnum::class)]
    private MigrationStatusEnum $migrationStatus = MigrationStatusEnum::MIGRATION_NOT_CREATED;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function setSubdomain(?string $subdomain): static
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    public function getDbName(): ?string
    {
        return $this->dbName;
    }

    public function setDbName(?string $dbName): static
    {
        $this->dbName = $dbName;

        return $this;
    }

    public function getDbStatus(): ?DatabaseStatusEnum
    {
        return $this->dbStatus;
    }

    public function setDbStatus(DatabaseStatusEnum $dbStatus): static
    {
        $this->dbStatus = $dbStatus;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function generateDatabase($name){
        $this->dbName = $name . "_tenant_" . $this->getId();
    }

    public function getFixturesStatus(): ?FixturesStatusEnum
    {
        return $this->fixturesStatus;
    }

    public function setFixturesStatus(FixturesStatusEnum $fixturesStatus): static
    {
        $this->fixturesStatus = $fixturesStatus;

        return $this;
    }

    public function getMigrationStatus(): ?MigrationStatusEnum
    {
        return $this->migrationStatus;
    }

    public function setMigrationStatus(MigrationStatusEnum $migrationStatus): static
    {
        $this->migrationStatus = $migrationStatus;

        return $this;
    }
}
