<?php

namespace ContainerPkxW64u;


use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getServicesResetterService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'services_resetter' shared service.
     *
     * @return \Symfony\Component\HttpKernel\DependencyInjection\ServicesResetter
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'symfony'.\DIRECTORY_SEPARATOR.'http-kernel'.\DIRECTORY_SEPARATOR.'DependencyInjection'.\DIRECTORY_SEPARATOR.'ServicesResetter.php';

        return $container->services['services_resetter'] = new \Symfony\Component\HttpKernel\DependencyInjection\ServicesResetter(new RewindableGenerator(function () use ($container) {
            if (isset($container->services['request_stack'])) {
                yield 'request_stack' => ($container->services['request_stack'] ?? null);
            }
            if (isset($container->privates['container.env_var_processor'])) {
                yield 'container.env_var_processor' => ($container->privates['container.env_var_processor'] ?? null);
            }
            if (isset($container->services['cache.app'])) {
                yield 'cache.app' => ($container->services['cache.app'] ?? null);
            }
            if (isset($container->services['cache.system'])) {
                yield 'cache.system' => ($container->services['cache.system'] ?? null);
            }
            if (false) {
                yield 'cache.validator' => null;
            }
            if (false) {
                yield 'cache.serializer' => null;
            }
            if (false) {
                yield 'cache.property_info' => null;
            }
            if (isset($container->services['debug.stopwatch'])) {
                yield 'debug.stopwatch' => ($container->services['debug.stopwatch'] ?? null);
            }
            if (isset($container->services['event_dispatcher'])) {
                yield 'debug.event_dispatcher' => ($container->services['event_dispatcher'] ?? null);
            }
            if (false) {
                yield 'debug.log_processor' => null;
            }
            if (isset($container->privates['session_listener'])) {
                yield 'session_listener' => ($container->privates['session_listener'] ?? null);
            }
            if (isset($container->services['cache.validator_expression_language'])) {
                yield 'cache.validator_expression_language' => ($container->services['cache.validator_expression_language'] ?? null);
            }
            if (isset($container->services['doctrine'])) {
                yield 'doctrine' => ($container->services['doctrine'] ?? null);
            }
            if (isset($container->privates['doctrine.debug_data_holder'])) {
                yield 'doctrine.debug_data_holder' => ($container->privates['doctrine.debug_data_holder'] ?? null);
            }
            if (isset($container->privates['twig'])) {
                yield 'twig' => ($container->privates['twig'] ?? null);
            }
            if (isset($container->privates['security.token_storage'])) {
                yield 'security.token_storage' => ($container->privates['security.token_storage'] ?? null);
            }
            if (isset($container->privates['cache.security_expression_language'])) {
                yield 'cache.security_expression_language' => ($container->privates['cache.security_expression_language'] ?? null);
            }
            if (isset($container->services['cache.security_is_granted_attribute_expression_language'])) {
                yield 'cache.security_is_granted_attribute_expression_language' => ($container->services['cache.security_is_granted_attribute_expression_language'] ?? null);
            }
            if (isset($container->services['cache.security_is_csrf_token_valid_attribute_expression_language'])) {
                yield 'cache.security_is_csrf_token_valid_attribute_expression_language' => ($container->services['cache.security_is_csrf_token_valid_attribute_expression_language'] ?? null);
            }
            if (isset($container->privates['debug.security.firewall'])) {
                yield 'debug.security.firewall' => ($container->privates['debug.security.firewall'] ?? null);
            }
            if (isset($container->privates['debug.security.firewall.authenticator.main'])) {
                yield 'debug.security.firewall.authenticator.main' => ($container->privates['debug.security.firewall.authenticator.main'] ?? null);
            }
            if (false) {
                yield 'api_platform.cache.route_name_resolver' => null;
            }
            if (isset($container->privates['api_platform.cache.metadata.resource'])) {
                yield 'api_platform.cache.metadata.resource' => ($container->privates['api_platform.cache.metadata.resource'] ?? null);
            }
            if (isset($container->privates['api_platform.cache.metadata.property'])) {
                yield 'api_platform.cache.metadata.property' => ($container->privates['api_platform.cache.metadata.property'] ?? null);
            }
            if (isset($container->privates['api_platform.cache.metadata.resource_collection'])) {
                yield 'api_platform.cache.metadata.resource_collection' => ($container->privates['api_platform.cache.metadata.resource_collection'] ?? null);
            }
            if (false) {
                yield 'api_platform.cache.openapi' => null;
            }
            if (isset($container->privates['monolog.handler.main'])) {
                yield 'monolog.handler.main' => ($container->privates['monolog.handler.main'] ?? null);
            }
            if (isset($container->privates['monolog.handler.console'])) {
                yield 'monolog.handler.console' => ($container->privates['monolog.handler.console'] ?? null);
            }
            if (isset($container->privates['debug.security.event_dispatcher.main'])) {
                yield 'debug.security.event_dispatcher.main' => ($container->privates['debug.security.event_dispatcher.main'] ?? null);
            }
        }, fn () => 0 + (int) (isset($container->services['request_stack'])) + (int) (isset($container->privates['container.env_var_processor'])) + (int) (isset($container->services['cache.app'])) + (int) (isset($container->services['cache.system'])) + (int) (false) + (int) (false) + (int) (false) + (int) (isset($container->services['debug.stopwatch'])) + (int) (isset($container->services['event_dispatcher'])) + (int) (false) + (int) (isset($container->privates['session_listener'])) + (int) (isset($container->services['cache.validator_expression_language'])) + (int) (isset($container->services['doctrine'])) + (int) (isset($container->privates['doctrine.debug_data_holder'])) + (int) (isset($container->privates['twig'])) + (int) (isset($container->privates['security.token_storage'])) + (int) (isset($container->privates['cache.security_expression_language'])) + (int) (isset($container->services['cache.security_is_granted_attribute_expression_language'])) + (int) (isset($container->services['cache.security_is_csrf_token_valid_attribute_expression_language'])) + (int) (isset($container->privates['debug.security.firewall'])) + (int) (isset($container->privates['debug.security.firewall.authenticator.main'])) + (int) (false) + (int) (isset($container->privates['api_platform.cache.metadata.resource'])) + (int) (isset($container->privates['api_platform.cache.metadata.property'])) + (int) (isset($container->privates['api_platform.cache.metadata.resource_collection'])) + (int) (false) + (int) (isset($container->privates['monolog.handler.main'])) + (int) (isset($container->privates['monolog.handler.console'])) + (int) (isset($container->privates['debug.security.event_dispatcher.main']))), ['request_stack' => ['?resetRequestFormats'], 'container.env_var_processor' => ['reset'], 'cache.app' => ['reset'], 'cache.system' => ['reset'], 'cache.validator' => ['reset'], 'cache.serializer' => ['reset'], 'cache.property_info' => ['reset'], 'debug.stopwatch' => ['reset'], 'debug.event_dispatcher' => ['reset'], 'debug.log_processor' => ['reset'], 'session_listener' => ['reset'], 'cache.validator_expression_language' => ['reset'], 'doctrine' => ['reset'], 'doctrine.debug_data_holder' => ['reset'], 'twig' => ['resetGlobals'], 'security.token_storage' => ['disableUsageTracking', 'setToken'], 'cache.security_expression_language' => ['reset'], 'cache.security_is_granted_attribute_expression_language' => ['reset'], 'cache.security_is_csrf_token_valid_attribute_expression_language' => ['reset'], 'debug.security.firewall' => ['reset'], 'debug.security.firewall.authenticator.main' => ['reset'], 'api_platform.cache.route_name_resolver' => ['reset'], 'api_platform.cache.metadata.resource' => ['reset'], 'api_platform.cache.metadata.property' => ['reset'], 'api_platform.cache.metadata.resource_collection' => ['reset'], 'api_platform.cache.openapi' => ['reset'], 'monolog.handler.main' => ['reset'], 'monolog.handler.console' => ['reset'], 'debug.security.event_dispatcher.main' => ['reset']]);
    }
}
