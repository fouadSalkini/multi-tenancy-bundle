# multi-tenancy-bundle

![sf-multi-tenancy](https://github.com/fouadSalkini/multi-tenancy-bundle/assets/51783676/43351c3d-b947-483f-8c79-b863369ab83d)


A simple method for smoothly integrating multi-tenant databases into your Symfony application is provided by the Symfony Multi Tenancy Bundle. By managing numerous databases through a single entity manager, it facilitates the use of Doctrine and makes runtime switching between databases possible.
This package includes a wide range of functionalities, including the simple switching between tenant databases according to an event.

Buy me a cup of coffee🙂 ☕️: [https://www.buymeacoffee.com/fouadsalkini](https://www.buymeacoffee.com/fouadsalkini)

# Features:
- Supports all kinds of databases.
- Easy to use and handle
- Extendable bundle
- Not affecting the application performance
- Ability to switch between databases by dispatching a single event
- Ability to auto-generate tenant database using one command
- Ability to auto-generate migrations for each tenant database
- Ability to Seed data into a specific tenant using seed bundle.
- Ability to use messenger to run the processes in background.
- It uses the default entity manager connection.

# Requirements:
- PHP 8.1+
- Symfony 6+
- Doctrine bundle
- Doctrine Migrations bundle
- Yaml
- Apache
- Virtual host


# Installation:
```
composer require fds/multi-tenancy-bundle
```

# Usage:
## 1. env requirements:
- Add ``` BASE_HOST ``` to your .env file. Ex: ``` BASE_HOST=yourmaindomain.com ```.

## 2. Add doctrine connection wrapper
- open your ``` config/packages/doctrine.yaml ``` and add the ``` wrapper_class ```
  ```
  # config/packages/doctrine.yaml
  doctrine:
    dbal:
        wrapper_class: FDS\MultiTenancyBundle\DBAL\MultiDbConnectionWrapper
  ```

## 3. Tenant Entity
- Create Tenant Entity or use whatever Entity you want to configure the bundle with:.
- use ``` TenantConfigTrait ``` inside your Tenant entity to implement the full db attributes requirements.
- 
  ```
  // src/App/Entity/Tenant
  namespace App\Entity;
  use Doctrine\ORM\Mapping as ORM;
  use FDS\MultiTenancyBundle\Traits\TenantConfigTrait;

  class Tenant
  {
    use TenantConfigTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
  ```

## 4. Update fds_multi_tenancy.yaml
  - Add your Tenant entity path to ``` config/packages/fds_multi_tenancy.yaml ``` file.
    ```
      # config/packages/fds_multi_tenancy.yaml
      fds_multi_tenancy:
        tenant_entity: App\Entity\Tenant # set your custom path for your Tenant entity created in step 2.
    ```

## 5. Tenant Entity Repository
  - Your ``` TenantRepository ``` should impements the ``` TenantRepositoryInterface ``` interface.
    ```
    namespace App\Repository;

    use App\Entity\Tenant;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    use FDS\MultiTenancyBundle\Model\TenantRepositoryInterface;

    /**
     * @extends ServiceEntityRepository<Tenant>
     */
    class TenantRepository extends ServiceEntityRepository implements TenantRepositoryInterface{

    // your custom functions here
    ```
  - Define ``` findBySubdomain ``` and ``` findByIdentifier ``` functions in your repository
    ```
    public function findBySubdomain($subdomain){
        // subdomain is required here
        // you can add your specific filters here like "status","isActive",...
        return $this->findOneBy(["subdomain" => $subdomain]);
    }

    public function findByIdentifier($identifier){
        // use your identifier (unique value) or whathever you want(email, username,id,...)
        return $this->findOneBy(["yourCustomIdentifier" => $identifier]);
    }
    ```

## 6. Create tenant database
- create a record in your tenant entity (email, name, subdomain, dbName):
  ```
    $tenant = new Tenant();
    $tenant->setEmail("email@example.com");
    $tenant->setName("First tenant");
    $tenant->setSubdomain("first");
    $tenant->setDbName("tenant_1");
    $em->persist($tenant);
    $em->flush();
  ```
- use this command to create a database for a specific tenant
  ```
    php bin/console tenant:database:create
  ```
- You will be prompted to enter the tenant identifier(username|id|email|..) 

## 7. Add RouterSubscriber class to your project (optional)
  - Define a class that implements ``` EventSubscriberInterface ``` in order to switch between databases automatically based on subdomain assigned to a specific Tenant
    ```
    // src/EventSubscriber/RouterSubscriber.php
    namespace App\EventSubscriber;
    
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpKernel\Event\ControllerEvent;

    use FDS\MultiTenancyBundle\Service\TenantService;

    
    class RouterSubscriber implements EventSubscriberInterface
    {
        public function __construct(
            // inject the TenantService in your constructor
            private TenantService $tenantService
            )
        {
        }
        public static function getSubscribedEvents()
        {
            return array(
                KernelEvents::CONTROLLER => array(array('onKernelController', 1)),
            );
        }

        public function onKernelController(ControllerEvent $event)
        {
          $request = $event->getRequest();
    
          // call the checkCurrentTenant function to detect the domain changes and switch to the tenant specific database.
          $this->tenantService->checkCurrentTenant($request);
        }
    }
    ```

## 8. Manually switch between databases (optional)
  - you can manually switch between databases by calling this function
    ```
    // $em is the main entity manager
    $connection = $em->getConnection();
    $connection->changeDatabase("your database name");
    ```

  
Other Instructions will be added soon.
