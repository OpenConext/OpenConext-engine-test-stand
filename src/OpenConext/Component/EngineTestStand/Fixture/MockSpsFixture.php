<?php

namespace OpenConext\EngineTestStand\Fixture;

class MockSpsFixtureAbstract extends AbstractRoleFixture
{
    public function configureFromAuthnRequest($spName, \SAML2_AuthnRequest $authnRequest)
    {
        $entityId = $authnRequest->getIssuer();

        $entity = new \SAML2_XML_md_EntityDescriptor();
        $entity->entityID = $entityId;
        $entity->Extensions[] = $authnRequest;

        $this->fixture[$spName] = $entity;
    }
}