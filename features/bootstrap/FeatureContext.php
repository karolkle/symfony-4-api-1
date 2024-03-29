<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;


class FeatureContext extends \Behatch\Context\RestContext
{
    const USERS = [
        'karol_kle' => 'blabla'
        ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
    {
        "username": "%s",
        "password": "%s"
    }
    ';

    /**
     * @var \App\DataFixtures\AppFixtures
     */
    private $fixtures;

    /**
     * @var \Coduo\PHPMatcher\Matcher
     */
    private $matcher;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface 
     */

    private $entityManager;

    public function __construct(\Behatch\HttpCall\Request $request,
                                \App\DataFixtures\AppFixtures $fixtures,
                                \Doctrine\ORM\EntityManagerInterface $entityManager)
   {

        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = (new \Coduo\PHPMatcher\Factory\MatcherFactory())->createMatcher();
        $this->em = $entityManager;

   }

    /**
     * @Given I am authenticated as :arg1
     */
    public function iAmAuthenticatedAs($user)
    {
       $this->request->setHttpHeader('Content-Type', 'application/ld+json');
       $this->request->send(
           'POST',
            $this->locatePath(self::AUTH_URL),
           [],
           [],
           sprintf(self::AUTH_URL, $user, self::USERS[$user])
       );
       $json = json_decode($this->request->getContent(), true);
       $this->assertTrue(isset($json['token']));
       $token= $json['token'];
       $this->request->setHttpHeader(
           'Authorization',
           'Bearer'.$token
       );
    }

    /**
     * @Then the JSON matches expected template:
     */
    public function theJsonMatchesExpectedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();
        var_dump($actual);
        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }


    public function createSchema()
   {
       $classes = $this->em->getMetadataFactory()->getAllMetadata();
       $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
       $schemaTool->dropSchema($classes);
       $schemaTool->createSchema($classes);

       $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
       $fixturesExecutor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
       $fixturesExecutor->execute([
          $this->fixtures
       ]);
   }
}
