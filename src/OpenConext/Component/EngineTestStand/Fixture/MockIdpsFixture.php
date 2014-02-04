<?php

namespace OpenConext\Component\EngineTestStand\Fixture;

class MockIdpsFixture extends AbstractRoleFixture
{
    public function register($name, $entityId)
    {
        $entityMetadata = new \SAML2_XML_md_EntityDescriptor();
        $entityMetadata->entityID = $entityId;

        $acsService = new \SAML2_XML_md_IndexedEndpointType();
        $acsService->index = 0;
        $acsService->Binding  = \SAML2_Const::BINDING_HTTP_REDIRECT;
        $acsService->Location = $request->getSchemeAndHttpHost() . "/idp.php/{$idpName}/sso";

        $idpSsoDescriptor = new \SAML2_XML_md_IDPSSODescriptor();
        $idpSsoDescriptor->protocolSupportEnumeration = array(\SAML2_Const::NS_SAMLP);
        $idpSsoDescriptor->SingleSignOnService[] = $acsService;

        $entityMetadata->RoleDescriptor[] = $idpSsoDescriptor;

        parent::register($name, $entityId); // TODO: Change the autogenerated stub
    }


    public function configureFromResponse($idpName, \SAML2_Response $response)
    {
        $entity = new \SAML2_XML_md_EntityDescriptor();
        $entity->entityID = $response->getIssuer();

        $entity->Extensions[] = $response;
        $this->fixture[$idpName] = $entity;
    }

    public function overrideResponseDestination($idpName, $acsUrl)
    {
        if (!isset($this->fixture[$idpName])) {
            throw new \RuntimeException("IDP $idpName does not exist?");
        }

        /** @var \SAML2_XML_md_EntityDescriptor $fixture */
        $fixture = $this->fixture[$idpName];
        $fixture->Extensions['DestinationOverride'] = $acsUrl;
    }
}
