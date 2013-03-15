<?php

namespace Cethyworks\AnalyticsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\UrlValidator;
use Symfony\Component\Config\Definition\Processor;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CethyworksAnalyticsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if(isset($config['segmentIo']) && isset($config['trackers']) && !empty($config['trackers']))
            throw new \InvalidArgumentException(sprintf('Not possible to load segmenIo and trackers'));
            
        if(isset($config['segmentIo']))
        {
            foreach ($config['segmentIo'] as $name => $segmentIo) {
                $container->setParameter('cethyworks_analytics.segmentIo.account_id', $segmentIo);
                $this->loadSegmentIo($name, $segmentIo, $container, $config['environments']);
            }
        }
        else
        {
            foreach ($config['trackers'] as $name => $tracker) {
                $this->loadTracker($name, $tracker, $container, $config['environments']);
            }
        }
    }
    
    public function loadSegmentIo($name=null, $segmentIo, ContainerBuilder $container, array $environments, $class=null)
    {
        $name= $segmentIo;
        $class = $class ? : $container->getParameter('cethyworks.analytics_tracker.class');
        $template = $container->getParameter('cethyworks.analytics_auto.template');

        $this->addTrackerDefinition($name, $class, $environments, $template, $params=null, $container);
    }

    public function loadGoogleAnalyticsTracker($name, $class, array $environments, $template, array $params, ContainerBuilder $container)
    {
          $this->ensureParameters($name, array('account'), $params);

        $class = $class ? : $container->getParameter('cethyworks.analytics_tracker.class');
        $template = $template ? : $container->getParameter('cethyworks.googleAnalytics_tracker.template');

        $this->addTrackerDefinition($name, $class, $environments, $template, $params, $container);
    }

    public function loadTracker($name, array $tracker, ContainerBuilder $container, array $environments)
    {
        $methodName = sprintf('load%sTracker', $container->camelize($tracker['type']));
        
        if(! method_exists($this, $methodName))
        {
            throw new \InvalidArgumentException(sprintf('The \'%s\' tracker type is not supported.', $tracker['type']));
        }
        
         $this->$methodName(
            $name,
            $tracker['class'],
            array_merge($tracker['environments'], $environments),
            $tracker['template'],
            $tracker['params'],
            $container
        );
    }
    
     /**
     * Adds a tracker definition to the given container builder
     *
     * @param  string           $name         The name of the tracker
     * @param  string           $class        The listener class
     * @param  array            $environments The environments where the tracker is activated
     * @param  string           $template     The template of the tracker
     * @param  array            $params       The parameters for the template
     * @param  ContainerBuilder $container    A ContainerBuilder instance
     */
    protected function addTrackerDefinition($name, $class, array $environments, $template, $params, ContainerBuilder $container)
    {
        if(!in_array($container->getParameter('kernel.environment') , $environments))
        {
            return;
        }
        
        $params['name'] = $name;

        $templating = new Reference('templating.engine.twig');
        $definition = new Definition($class, array($templating, $template, $params));
        $definition->addTag('kernel.event_listener', array(
            'event' => 'kernel.response',
            'method' => 'onKernelResponse'
        ));

        $container->setDefinition(
            sprintf('cethyworks.analytics_tracker.%s_tracker', $name),
            $definition
        );
    }

   /**
     * Ensures the specified parameters are filled
     *
     * @param  string $name   The name of the tracker
     * @param  string $keys   The names of the params
     * @param  strign $params The params
     *
     * @throws Exception if any param is not filled
     */
    protected function ensureParameters($name, array $keys, array $params)
    {
        foreach ($keys as $key) {
            if (empty($params[$key])) {
                throw new \Exception(sprintf('You must specify a \'%s\' parameter for the \'%s\' tracker.', $key, $name));
            }
        }
    }
    
   /**
     * Indicates whether the specified url is valid
     *
     * @param string $url The url to validate
     *
     * @return string
     */
    public function isUrlValid($url)
    {
        $validator = new UrlValidator();

        return $validator->isValid($url, new Url());
    }
}
