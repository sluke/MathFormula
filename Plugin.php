<?php

namespace TypechoPlugin\MathFormula;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form\Element\Textarea;
use Widget\Options;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 数学公式插件
 * 支持MathJax和KaTeX两种渲染引擎
 *
 * @package MathFormula
 * @author sluke
 * @version 1.0.0
 * @link https://typecho.org
 */
class Plugin implements PluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     */
    public static function activate()
    {
        // 注册前台页面底部钩子
        \Typecho\Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'renderFooter');
        
        // 注册后台编辑页面底部钩子
        \Typecho\Plugin::factory('Widget_Contents_Post_Edit')->footer = array(__CLASS__, 'renderFooter');
        
        return _t('数学公式插件已激活');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     */
    public static function deactivate()
    {
        return _t('数学公式插件已禁用');
    }

    /**
     * 获取插件配置面板
     *
     * @param Form $form 配置面板
     */
    public static function config(Form $form)
    {
        // 渲染引擎选择
        $engine = new Radio('engine', 
            [
                'mathjax' => _t('MathJax'), 
                'katex' => _t('KaTeX')
            ], 
            'mathjax', _t('渲染引擎'), _t('选择用于渲染数学公式的引擎'));
        $form->addInput($engine);
        
        // 加载方式选择
        $loadMethod = new Radio('loadMethod', 
            [
                'local-first' => _t('本地优先'), 
                'cdn-first' => _t('CDN优先'), 
                'local-only' => _t('仅本地'), 
                'cdn-only' => _t('仅CDN')
            ], 
            'local-first', _t('加载方式'), _t('选择库文件的加载方式，本地优先模式会在CDN不可用时自动切换到本地加载'));
        $form->addInput($loadMethod);
        
        // MathJax CDN地址
        $mathjaxCdn = new Text('mathjaxCdn', 
            null, 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js', 
            _t('MathJax CDN地址'), _t('MathJax库的CDN地址'));
        $form->addInput($mathjaxCdn);
        
        // MathJax配置选项
        $mathjaxConfig = new Textarea('mathjaxConfig', 
            null, '{\n    tex: {\n        inlineMath: [[\'$\', \'$\'], [\'\\(\', \'\\)\']],\n        displayMath: [[\'$$\', \'$$\'], [\'\\[\', \'\\]\']],\n        tags: \'ams\',\n        tagSide: \'right\',\n        tagIndent: \'0.8em\'\n    },\n    svg: {\n        fontCache: \'global\'\n    },\n    chtml: {\n        displayAlign: \'center\'\n    }\n}', 
            _t('MathJax配置'), _t('MathJax的配置选项，JSON格式'));
        $form->addInput($mathjaxConfig);
        
        // KaTeX CSS CDN地址
        $katexCssCdn = new Text('katexCssCdn', 
            null, 'https://cdn.jsdelivr.net/npm/katex@0.16.4/dist/katex.min.css', 
            _t('KaTeX CSS CDN地址'), _t('KaTeX样式文件的CDN地址'));
        $form->addInput($katexCssCdn);
        
        // KaTeX JS CDN地址
        $katexJsCdn = new Text('katexJsCdn', 
            null, 'https://cdn.jsdelivr.net/npm/katex@0.16.4/dist/katex.min.js', 
            _t('KaTeX JS CDN地址'), _t('KaTeX核心库的CDN地址'));
        $form->addInput($katexJsCdn);
        
        // KaTeX 自动渲染CDN地址
        $katexAutoRenderCdn = new Text('katexAutoRenderCdn', 
            null, 'https://cdn.jsdelivr.net/npm/katex@0.16.4/dist/contrib/auto-render.min.js', 
            _t('KaTeX自动渲染CDN地址'), _t('KaTeX自动渲染插件的CDN地址'));
        $form->addInput($katexAutoRenderCdn);
        
        // 在后台编辑页面启用实时预览
        $enablePreview = new Checkbox('enablePreview', 
            ['1' => _t('启用后台编辑实时预览')], 
            ['1'], _t('后台编辑预览'), _t('是否在后台编辑页面启用公式实时预览'));
        $form->addInput($enablePreview);
        
        // 公式编号开关
        $enableNumbering = new Checkbox('enableNumbering', 
            ['1' => _t('启用公式自动编号')], 
            [], _t('公式编号'), _t('是否启用公式自动编号功能'));
        $form->addInput($enableNumbering);
        
        // 编号格式
        $numberingFormat = new Text('numberingFormat', 
            null, '(#)', _t('编号格式'), _t('公式编号的格式，如：(1), [1], 1等'));
        $form->addInput($numberingFormat);
        
        // 深色模式支持
        $darkMode = new Radio('darkMode', 
            [
                'auto' => _t('自动检测'), 
                'light' => _t('浅色模式'), 
                'dark' => _t('深色模式')
            ], 
            'auto', _t('深色模式'), _t('选择深色模式支持方式'));
        $form->addInput($darkMode);
        
        // KaTeX 渲染模式
        $katexRenderMode = new Radio('katexRenderMode', 
            [
                'html' => _t('HTML/CSS'), 
                'svg' => _t('SVG')
            ], 
            'html', _t('KaTeX渲染模式'), _t('选择KaTeX的渲染模式'));
        $form->addInput($katexRenderMode);
        
        // KaTeX 字体大小
        $katexFontSize = new Text('katexFontSize', 
            null, '1.2em', _t('KaTeX字体大小'), _t('设置KaTeX公式的全局字体大小'));
        $form->addInput($katexFontSize);
        
        // KaTeX 错误处理
        $katexErrorHandling = new Radio('katexErrorHandling', 
            [
                'strict' => _t('严格模式（显示错误）'), 
                'ignore' => _t('忽略错误'), 
                'render' => _t('渲染错误信息')
            ], 
            'render', _t('KaTeX错误处理'), _t('选择KaTeX的错误处理方式'));
        $form->addInput($katexErrorHandling);
        
        // 调试模式
        $debugMode = new Checkbox('debugMode', 
            ['1' => _t('启用调试模式')], 
            [], _t('调试模式'), _t('启用后会在浏览器控制台输出调试信息'));
        $form->addInput($debugMode);
    }

    /**
     * 个人用户的配置面板
     *
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
        // 个人用户配置，暂时不需要
    }
    
    /**
     * 渲染页面底部，添加公式渲染脚本
     *
     * @param mixed $widget 当前Widget对象
     */
    public static function renderFooter($widget)
    {
        $options = Options::alloc();
        $pluginOptions = $options->plugin('MathFormula');
        $engine = $pluginOptions->engine;
        
        // 添加深色模式样式
        $darkModeScript = self::getDarkModeScript($pluginOptions);
        
        // 根据选择的引擎渲染不同的脚本
        if ($engine == 'mathjax') {
            $script = self::renderMathJax($pluginOptions);
        } else {
            $script = self::renderKaTeX($pluginOptions);
        }
        
        // 直接输出脚本，不需要返回值
        echo $darkModeScript . $script;
    }
    
    /**
     * 获取深色模式脚本和样式
     *
     * @param mixed $options 插件配置
     * @return string
     */
    private static function getDarkModeScript($options)
    {
        $darkMode = isset($options->darkMode) ? $options->darkMode : 'auto';
        
        // 简化深色模式处理，使用CSS变量和更简单的实现
        return <<<HTML
<style>
    /* 深色模式基础样式 */
    @media (prefers-color-scheme: dark) {
        /* MathJax深色模式 */
        .MathJax, .MathJax_CHTML {
            color: #ffffff !important;
        }
        .MathJax_SVG path {
            fill: #ffffff !important;
        }
        
        /* KaTeX深色模式 */
        .katex, .katex .katex-mathml, .katex .katex-html {
            color: #ffffff !important;
        }
        .katex-display .katex-equation-number {
            color: #cccccc !important;
        }
    }
    
    /* 强制深色模式 */
    body.math-formula-dark-mode {
        .MathJax, .MathJax_CHTML, .katex, .katex .katex-mathml, .katex .katex-html {
            color: #ffffff !important;
        }
        .MathJax_SVG path {
            fill: #ffffff !important;
        }
        .katex-display .katex-equation-number {
            color: #cccccc !important;
        }
    }
    
    /* 强制浅色模式 */
    body.math-formula-light-mode {
        .MathJax, .MathJax_CHTML, .katex, .katex .katex-mathml, .katex .katex-html {
            color: #000000 !important;
        }
        .MathJax_SVG path {
            fill: #000000 !important;
        }
        .katex-display .katex-equation-number {
            color: #666666 !important;
        }
    }
</style>
<script>
    // 简化深色模式处理
    const darkMode = '{$darkMode}';
    const body = document.body;
    
    // 移除之前的模式类
    body.classList.remove('math-formula-dark-mode', 'math-formula-light-mode');
    
    if (darkMode === 'dark') {
        body.classList.add('math-formula-dark-mode');
    } else if (darkMode === 'light') {
        body.classList.add('math-formula-light-mode');
    } else {
        // 自动检测
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const updateMode = () => {
            body.classList.remove('math-formula-dark-mode', 'math-formula-light-mode');
            body.classList.add(mediaQuery.matches ? 'math-formula-dark-mode' : 'math-formula-light-mode');
        };
        
        updateMode();
        mediaQuery.addEventListener('change', updateMode);
    }
</script>
HTML;
    }
    
    /**
     * 渲染MathJax脚本
     *
     * @param mixed $options 插件配置
     * @return string
     */
    private static function renderMathJax($options)
    {
        // 本地和CDN地址配置
        $localPath = $options->pluginUrl . '/MathFormula/lib/mathjax/tex-mml-chtml.js';
        $cdn = 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js';
        $enableNumbering = isset($options->enableNumbering) && $options->enableNumbering;
        $loadMethod = isset($options->loadMethod) ? $options->loadMethod : 'cdn-first'; // 默认CDN优先，避免本地文件路径问题
        
        // 简化配置 - 确保支持所有必要功能
        $tagsOption = $enableNumbering ? 'ams' : 'none';
        $mathjaxConfig = <<<JS
window.MathJax = {
    tex: {
        inlineMath: [["$", "$"], ["\\(", "\\)"]],
        displayMath: [["$$", "$$"], ["\\[", "\\]"]],
        packages: ['base', 'ams', 'noerrors', 'noundefined', 'text'],
        processEscapes: true,
        processEnvironments: true,
        tags: '{$tagsOption}',
        tagSide: 'right',
        tagIndent: '0.8em'
    },
    svg: {
        fontCache: 'global'
    },
    chtml: {
        displayAlign: 'center'
    },
    options: {
        enableMenu: true
    }
};
JS;
        
        // 根据加载方式生成不同的脚本，简化逻辑，移除不必要的onload处理
        switch ($loadMethod) {
            case 'local-only':
                // 仅本地加载
                $script = <<<HTML
<script>
    {$mathjaxConfig}
</script>
<script src="{$localPath}"></script>
HTML;
                break;
            case 'cdn-only':
            case 'cdn-first':
            default:
                // CDN加载（包括CDN-only和cdn-first模式）
                $script = <<<HTML
<script>
    {$mathjaxConfig}
</script>
<script src="{$cdn}" async></script>
HTML;
                break;
        }
        
        return $script;
    }
    
    /**
     * 渲染KaTeX脚本
     *
     * @param mixed $options 插件配置
     * @return string
     */
    private static function renderKaTeX($options)
    {
        // 获取插件URL
        $pluginUrl = $options->pluginUrl . '/MathFormula';
        
        // 本地和CDN地址配置
        $localCssPath = $pluginUrl . '/lib/katex/katex.min.css';
        $localJsPath = $pluginUrl . '/lib/katex/katex.min.js';
        $localAutoRenderPath = $pluginUrl . '/lib/katex/auto-render.min.js';
        
        $cssCdn = $options->katexCssCdn;
        $jsCdn = $options->katexJsCdn;
        $autoRenderCdn = $options->katexAutoRenderCdn;
        $enableNumbering = isset($options->enableNumbering) && $options->enableNumbering;
        $renderMode = isset($options->katexRenderMode) ? $options->katexRenderMode : 'html';
        $fontSize = isset($options->katexFontSize) ? $options->katexFontSize : '1.2em';
        $errorHandling = isset($options->katexErrorHandling) ? $options->katexErrorHandling : 'render';
        $loadMethod = isset($options->loadMethod) ? $options->loadMethod : 'cdn-first'; // 默认CDN优先，避免本地文件路径问题
        
        // 错误处理配置
        $throwOnError = $errorHandling === 'strict';
        $errorColor = '#cc0000';
        
        // 生成公式编号的JavaScript代码
        $numberingScript = '';
        if ($enableNumbering) {
            $numberingScript = '// 为块级公式添加编号
                    const parent = node.parentNode;
                    if (parent && parent.classList.contains(\'katex-display\')) {
                        // 创建包含公式和编号的容器
                        const container = document.createElement(\'div\');
                        container.className = \'katex-equation\';
                        
                        // 移动公式到容器
                        container.appendChild(node);
                        
                        // 创建编号元素
                        const number = document.createElement(\'span\');
                        number.className = \'katex-equation-number\';
                        number.textContent = \'(\' + equationCounter + \')\';
                        container.appendChild(number);
                        
                        // 替换原节点
                        parent.appendChild(container);
                        
                        // 递增计数器
                        equationCounter++;
                    }';
        }
        
        // 生成渲染配置
        $renderConfig = <<<JS
        {
            delimiters: [
                {left: "$", right: "$", display: false},
                {left: "$$", right: "$$", display: true},
                {left: "\\(", right: "\\)", display: false},
                {left: "\\[", right: "\\]", display: true}
            ],
            throwOnError: {$throwOnError},
            errorColor: "{$errorColor}",
            output: "{$renderMode}",
            trust: true,
            strict: {$throwOnError},
            preProcess: function(math) {
                return math;
            },
            postProcess: function(node, options) {
                if (options.display) {
                    {$numberingScript}
                }
            }
        }
JS;
        
        // 生成CSS链接
        $cssLink = '';
        if ($loadMethod === 'local-only') {
            $cssLink = "<link rel=\"stylesheet\" href=\"{$localCssPath}\">";
        } else {
            $cssLink = "<link rel=\"stylesheet\" href=\"{$cssCdn}\" onerror=\"this.onerror=null;this.href='{$localCssPath}';\">";
        }
        
        // 生成JS加载脚本，简化逻辑
        $jsScript = '';
        if ($loadMethod === 'local-only') {
            // 仅本地加载
            $jsScript = "<script src=\"{$localJsPath}\"></script>\n<script src=\"{$localAutoRenderPath}\"></script>";
        } else {
            // CDN加载（包括CDN-only和cdn-first模式）
            $jsScript = "<script src=\"{$jsCdn}\" async></script>\n<script src=\"{$autoRenderCdn}\" async></script>";
        }
        
        // 生成最终脚本
        $script = <<<HTML
{$cssLink}
{$jsScript}
<style>
    .katex { font-size: {$fontSize} !important; }
    .katex-display { overflow: auto hidden; padding: 0.5em 0; }
    .katex-display .katex { display: inline-block; }
    .katex-display .katex-equation { display: flex; justify-content: center; align-items: center; }
    .katex-display .katex-equation-number { margin-left: 1em; font-size: 0.8em; }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let equationCounter = 1;
        if (typeof renderMathInElement === 'function') {
            renderMathInElement(document.body, {$renderConfig});
        }
    });
</script>
HTML;
        
        return $script;
    }
}