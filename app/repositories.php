<?php
declare (strict_types = 1);

use App\Domain\Agronomist\AgronomistRepository;
use App\Domain\BankAccount\BankAccountRepository;
use App\Domain\CarbonCredit\CarbonCreditRepository;
use App\Domain\CommissionBatch\CommissionBatchRepository;
use App\Domain\CommissionStructure\CommissionStructureRepository;
use App\Domain\Commission\CommissionRepository;
use App\Domain\DistributorRank\DistributorRankRepository;
use App\Domain\DistributorTransactionType\DistributorTransactionTypeRepository;
use App\Domain\DistributorTransaction\DistributorTransactionRepository;
use App\Domain\Distributor\DistributorRepository;
use App\Domain\District\DistrictRepository;
use App\Domain\FarmerOrderReplacement\FarmerOrderReplacementRepository;
use App\Domain\FarmerOrder\FarmerOrderRepository;
use App\Domain\FarmerStory\FarmerStoryRepository;
use App\Domain\FarmerTransaction\FarmerTransactionRepository;
use App\Domain\Farmer\FarmerRepository;
use App\Domain\FarmVisitAttribute\FarmVisitAttributeRepository;
use App\Domain\FarmVisitFertilizer\FarmVisitFertilizerRepository;
use App\Domain\FarmVisitPesticide\FarmVisitPesticideRepository;
use App\Domain\FarmVisit\FarmVisitRepository;
use App\Domain\Farm\FarmRepository;
use App\Domain\Fertilizer\FertilizerRepository;
use App\Domain\InstallmentType\InstallmentTypeRepository;
use App\Domain\LeadFarmer\LeadFarmerRepository;
use App\Domain\LeadSource\LeadSourceRepository;
use App\Domain\Media\MediaRepository;
use App\Domain\Notification\NotificationRepository;
use App\Domain\NurseryManager\NurseryManagerRepository;
use App\Domain\Package\PackageRepository;
use App\Domain\PaymentType\PaymentTypeRepository;
use App\Domain\Pesticide\PesticideRepository;
use App\Domain\PlantGrowthRecord\PlantGrowthRecordRepository;
use App\Domain\PlantInventory\PlantInventoryRepository;
use App\Domain\PlantStockTransactionType\PlantStockTransactionTypeRepository;
use App\Domain\PlantStock\PlantStockRepository;
use App\Domain\Product\ProductRepository;
use App\Domain\QuantityUnit\QuantityUnitRepository;
use App\Domain\SalesTeamMember\SalesTeamMemberRepository;
use App\Domain\SalesTeam\SalesTeamRepository;
use App\Domain\SoilReportDocument\SoilReportDocumentRepository;
use App\Domain\State\StateRepository;
use App\Domain\TableAudit\TableAuditRepository;
use App\Domain\TableSetting\TableSettingRepository;
use App\Domain\Taluka\TalukaRepository;
use App\Domain\TaxType\TaxTypeRepository;
use App\Domain\Tax\TaxRepository;
use App\Domain\User\UserRepository;
use App\Domain\WaterSource\WaterSourceRepository;
use App\Infrastructure\Persistence\Agronomist\InMemoryAgronomistRepository;
use App\Infrastructure\Persistence\BankAccount\InMemoryBankAccountRepository;
use App\Infrastructure\Persistence\CarbonCredit\InMemoryCarbonCreditRepository;
use App\Infrastructure\Persistence\CommissionBatch\InMemoryCommissionBatchRepository;
use App\Infrastructure\Persistence\CommissionStructure\InMemoryCommissionStructureRepository;
use App\Infrastructure\Persistence\Commission\InMemoryCommissionRepository;
use App\Infrastructure\Persistence\DistributorRank\InMemoryDistributorRankRepository;
use App\Infrastructure\Persistence\DistributorTransactionType\InMemoryDistributorTransactionTypeRepository;
use App\Infrastructure\Persistence\DistributorTransaction\InMemoryDistributorTransactionRepository;
use App\Infrastructure\Persistence\Distributor\InMemoryDistributorRepository;
use App\Infrastructure\Persistence\District\InMemoryDistrictRepository;
use App\Infrastructure\Persistence\FarmerOrderReplacement\InMemoryFarmerOrderReplacementRepository;
use App\Infrastructure\Persistence\FarmerOrder\InMemoryFarmerOrderRepository;
use App\Infrastructure\Persistence\FarmerStory\InMemoryFarmerStoryRepository;
use App\Infrastructure\Persistence\FarmerTransaction\InMemoryFarmerTransactionRepository;
use App\Infrastructure\Persistence\Farmer\InMemoryFarmerRepository;
use App\Infrastructure\Persistence\FarmVisitAttribute\InMemoryFarmVisitAttributeRepository;
use App\Infrastructure\Persistence\FarmVisitFertilizer\InMemoryFarmVisitFertilizerRepository;
use App\Infrastructure\Persistence\FarmVisitPesticide\InMemoryFarmVisitPesticideRepository;
use App\Infrastructure\Persistence\FarmVisit\InMemoryFarmVisitRepository;
use App\Infrastructure\Persistence\Farm\InMemoryFarmRepository;
use App\Infrastructure\Persistence\Fertilizer\InMemoryFertilizerRepository;
use App\Infrastructure\Persistence\InstallmentType\InMemoryInstallmentTypeRepository;
use App\Infrastructure\Persistence\LeadFarmer\InMemoryLeadFarmerRepository;
use App\Infrastructure\Persistence\LeadSource\InMemoryLeadSourceRepository;
use App\Infrastructure\Persistence\Media\InMemoryMediaRepository;
use App\Infrastructure\Persistence\Notification\InMemoryNotificationRepository;
use App\Infrastructure\Persistence\NurseryManager\InMemoryNurseryManagerRepository;
use App\Infrastructure\Persistence\Package\InMemoryPackageRepository;
use App\Infrastructure\Persistence\PaymentType\InMemoryPaymentTypeRepository;
use App\Infrastructure\Persistence\Pesticide\InMemoryPesticideRepository;
use App\Infrastructure\Persistence\PlantGrowthRecord\InMemoryPlantGrowthRecordRepository;
use App\Infrastructure\Persistence\PlantInventory\InMemoryPlantInventoryRepository;
use App\Infrastructure\Persistence\PlantStockTransactionType\InMemoryPlantStockTransactionTypeRepository;
use App\Infrastructure\Persistence\PlantStock\InMemoryPlantStockRepository;
use App\Infrastructure\Persistence\Product\InMemoryProductRepository;
use App\Infrastructure\Persistence\QuantityUnit\InMemoryQuantityUnitRepository;
use App\Infrastructure\Persistence\SalesTeamMember\InMemorySalesTeamMemberRepository;
use App\Infrastructure\Persistence\SalesTeam\InMemorySalesTeamRepository;
use App\Infrastructure\Persistence\SoilReportDocument\InMemorySoilReportDocumentRepository;
use App\Infrastructure\Persistence\State\InMemoryStateRepository;
use App\Infrastructure\Persistence\TableAudit\InMemoryTableAuditRepository;
use App\Infrastructure\Persistence\TableSetting\InMemoryTableSettingRepository;
use App\Infrastructure\Persistence\Taluka\InMemoryTalukaRepository;
use App\Infrastructure\Persistence\TaxType\InMemoryTaxTypeRepository;
use App\Infrastructure\Persistence\Tax\InMemoryTaxRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use App\Infrastructure\Persistence\WaterSource\InMemoryWaterSourceRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions(
        [
            QuantityUnitRepository::class               => \DI\autowire(InMemoryQuantityUnitRepository::class),
            PlantGrowthRecordRepository::class          => \DI\autowire(InMemoryPlantGrowthRecordRepository::class),
            FarmVisitFertilizerRepository::class        => \DI\autowire(InMemoryFarmVisitFertilizerRepository::class),
            FarmVisitAttributeRepository::class         => \DI\autowire(InMemoryFarmVisitAttributeRepository::class),
            FarmVisitPesticideRepository::class         => \DI\autowire(InMemoryFarmVisitPesticideRepository::class),
            PesticideRepository::class                  => \DI\autowire(InMemoryPesticideRepository::class),
            FertilizerRepository::class                 => \DI\autowire(InMemoryFertilizerRepository::class),
            AgronomistRepository::class                 => \DI\autowire(InMemoryAgronomistRepository::class),
            NurseryManagerRepository::class             => \DI\autowire(InMemoryNurseryManagerRepository::class),
            FarmVisitRepository::class                  => \DI\autowire(InMemoryFarmVisitRepository::class),
            CommissionRepository::class                 => \DI\autowire(InMemoryCommissionRepository::class),
            CommissionStructureRepository::class        => \DI\autowire(InMemoryCommissionStructureRepository::class),
            CommissionBatchRepository::class            => \DI\autowire(InMemoryCommissionBatchRepository::class),
            SalesTeamRepository::class                  => \DI\autowire(InMemorySalesTeamRepository::class),
            SoilReportDocumentRepository::class         => \DI\autowire(InMemorySoilReportDocumentRepository::class),
            PlantStockRepository::class                 => \DI\autowire(InMemoryPlantStockRepository::class),
            PlantInventoryRepository::class             => \DI\autowire(InMemoryPlantInventoryRepository::class),
            PlantStockTransactionTypeRepository::class  => \DI\autowire(InMemoryPlantStockTransactionTypeRepository::class),
            SalesTeamMemberRepository::class            => \DI\autowire(InMemorySalesTeamMemberRepository::class),
            FarmerOrderReplacementRepository::class     => \DI\autowire(InMemoryFarmerOrderReplacementRepository::class),
            FarmerTransactionRepository::class          => \DI\autowire(InMemoryFarmerTransactionRepository::class),
            BankAccountRepository::class                => \DI\autowire(InMemoryBankAccountRepository::class),
            FarmerOrderRepository::class                => \DI\autowire(InMemoryFarmerOrderRepository::class),
            UserRepository::class                       => \DI\autowire(InMemoryUserRepository::class),
            DistributorRepository::class                => \DI\autowire(InMemoryDistributorRepository::class),
            FarmerRepository::class                     => \DI\autowire(InMemoryFarmerRepository::class),
            CarbonCreditRepository::class               => \DI\autowire(InMemoryCarbonCreditRepository::class),
            MediaRepository::class                      => \DI\autowire(InMemoryMediaRepository::class),
            LeadFarmerRepository::class                 => \DI\autowire(InMemoryLeadFarmerRepository::class),
            FarmRepository::class                       => \DI\autowire(InMemoryFarmRepository::class),
            StateRepository::class                      => \DI\autowire(InMemoryStateRepository::class),
            DistrictRepository::class                   => \DI\autowire(InMemoryDistrictRepository::class),
            TalukaRepository::class                     => \DI\autowire(InMemoryTalukaRepository::class),
            TableSettingRepository::class               => \DI\autowire(InMemoryTableSettingRepository::class),
            TableAuditRepository::class                 => \DI\autowire(InMemoryTableAuditRepository::class),
            WaterSourceRepository::class                => \DI\autowire(InMemoryWaterSourceRepository::class),
            NotificationRepository::class               => \DI\autowire(InMemoryNotificationRepository::class),
            ProductRepository::class                    => \DI\autowire(InMemoryProductRepository::class),
            PackageRepository::class                    => \DI\autowire(InMemoryPackageRepository::class),
            PaymentTypeRepository::class                => \DI\autowire(InMemoryPaymentTypeRepository::class),
            DistributorTransactionTypeRepository::class => \DI\autowire(InMemoryDistributorTransactionTypeRepository::class),
            DistributorTransactionRepository::class     => \DI\autowire(InMemoryDistributorTransactionRepository::class),
            InstallmentTypeRepository::class            => \DI\autowire(InMemoryInstallmentTypeRepository::class),
            LeadSourceRepository::class                 => \DI\autowire(InMemoryLeadSourceRepository::class),
            DistributorRankRepository::class            => \DI\autowire(InMemoryDistributorRankRepository::class),
            FarmerStoryRepository::class                => \DI\autowire(InMemoryFarmerStoryRepository::class),
            TaxTypeRepository::class                    => \DI\autowire(InMemoryTaxTypeRepository::class),
            TaxRepository::class                        => \DI\autowire(InMemoryTaxRepository::class),
        ]
    );
};