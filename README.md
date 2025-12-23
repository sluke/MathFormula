# 数学公式插件 (MathFormula)

Typecho数学公式插件，支持MathJax和KaTeX两种渲染引擎，让您的博客可以方便地显示数学公式。

## 功能特性

- ✅ 支持MathJax和KaTeX两种渲染引擎
- ✅ 支持行内公式（`$公式$`）和块级公式（`$$公式$$`）
- ✅ 支持前台页面和后台编辑页面的公式渲染
- ✅ 提供丰富的配置选项
- ✅ 支持自定义CDN地址
- ✅ 支持实时预览（在后台编辑页面）
- ✅ 支持公式自动编号和引用
- ✅ 支持深色模式适配（自动检测、浅色、深色）
- ✅ 支持KaTeX多种渲染模式（HTML/CSS、SVG）
- ✅ 支持自定义字体大小
- ✅ 支持多种错误处理方式
- ✅ 支持调试模式

## 安装方法

### 方法一：直接下载
1. 下载插件压缩包
2. 解压后将文件夹重命名为`MathFormula`
3. 上传到Typecho博客的`usr/plugins/`目录
4. 登录后台，在"插件管理"中启用插件
5. 点击"设置"按钮进行配置

### 方法二：Git克隆
```bash
git clone https://github.com/yourusername/typecho-plugin-mathformula.git usr/plugins/MathFormula
```

## 使用说明

### 基本语法

#### 行内公式
使用`$`符号包裹公式，例如：
```markdown
爱因斯坦的质能方程是 $E=mc^2$，它是相对论的核心公式之一。
```

#### 块级公式
使用`$$`符号包裹公式，例如：
```markdown
二次方程的求根公式：
$$
x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}
$$
```

### 公式编号和引用

#### 自动编号
在插件设置中启用"公式自动编号"后，块级公式将自动添加编号：

```markdown
$$
a^2 + b^2 = c^2
$$
```

这将显示为：
$$
a^2 + b^2 = c^2
tag{1}
$$

#### 手动引用
您可以在文中引用已编号的公式：

```markdown
如公式 (1) 所示，勾股定理描述了直角三角形三边的关系。
```

### 支持的公式示例

#### 微积分
```markdown
$$
\int_{-\infty}^{\infty} e^{-x^2} dx = \sqrt{\pi}
$$
```

#### 线性代数
```markdown
$$
\begin{pmatrix} a & b \\ c & d \end{pmatrix} \begin{pmatrix} x \\ y \end{pmatrix} = \begin{pmatrix} ax + by \\ cx + dy \end{pmatrix}
$$
```

#### 几何
```markdown
$$
\cos(\alpha + \beta) = \cos\alpha\cos\beta - \sin\alpha\sin\beta
$$
```

## 配置选项

### 渲染引擎
- **MathJax**：功能强大，支持更多数学符号和格式，但加载速度较慢
- **KaTeX**：轻量级，加载速度快，适合需要快速渲染的场景

### 加载方式
- **本地优先**：优先加载本地库文件，如果加载失败则自动切换到CDN
- **CDN优先**：优先加载CDN资源，如果加载失败则自动切换到本地文件
- **仅本地**：只加载本地库文件，不使用CDN
- **仅CDN**：只加载CDN资源，不使用本地文件

### MathJax配置
- **MathJax CDN地址**：MathJax库的CDN地址
- **MathJax配置**：JSON格式的MathJax配置，可自定义公式分隔符、编号等

### KaTeX配置
- **KaTeX CSS CDN地址**：KaTeX样式文件的CDN地址
- **KaTeX JS CDN地址**：KaTeX核心库的CDN地址
- **KaTeX自动渲染CDN地址**：KaTeX自动渲染插件的CDN地址
- **KaTeX渲染模式**：选择HTML/CSS或SVG渲染模式
- **KaTeX字体大小**：设置全局字体大小
- **KaTeX错误处理**：选择错误处理方式（严格模式、忽略错误、渲染错误信息）

### 公式编号
- **启用公式自动编号**：开关公式自动编号功能
- **编号格式**：自定义公式编号格式，如：(1), [1], 1等

### 深色模式
- **自动检测**：根据系统主题自动切换
- **浅色模式**：强制使用浅色模式
- **深色模式**：强制使用深色模式

### 其他配置
- **启用后台编辑实时预览**：在后台编辑页面实时预览公式
- **启用调试模式**：在浏览器控制台输出调试信息

## 本地依赖说明

插件已内置MathJax和KaTeX的本地库文件，位于`lib/`目录下：

- **MathJax**：`lib/mathjax/tex-mml-chtml.js`
- **KaTeX**：
  - `lib/katex/katex.min.css`
  - `lib/katex/katex.min.js`
  - `lib/katex/auto-render.min.js`

当选择"本地优先"或"仅本地"加载方式时，插件会使用这些本地库文件，避免CDN不可访问导致的公式渲染失败问题。

## 引擎对比

| 特性 | MathJax | KaTeX |
|------|---------|-------|
| 渲染速度 | 较慢 | 快 |
| 支持的符号数量 | 多 | 较多 |
| 文件大小 | 较大 | 较小 |
| 渲染质量 | 高 | 高 |
| 浏览器兼容性 | 好 | 好 |
| 公式编号支持 | 内置 | 需要扩展 |
| 深色模式支持 | 好 | 好 |

## 常见问题

### Q: 公式不显示怎么办？
A: 请检查以下几点：
1. 确保插件已正确启用
2. 检查CDN地址是否可访问
3. 确认公式语法是否正确
4. 查看浏览器控制台是否有错误信息
5. 如果启用了调试模式，查看调试信息

### Q: 如何自定义公式分隔符？
A: 在插件配置中修改MathJax或KaTeX的配置选项，自定义分隔符。

### Q: 如何启用公式编号？
A: 在插件设置中勾选"启用公式自动编号"选项即可。

### Q: 插件支持哪些Typecho版本？
A: 支持Typecho 1.2.0及以上版本。

### Q: 如何切换深色模式？
A: 在插件设置中选择深色模式支持方式，可选择自动检测、浅色模式或深色模式。

## 开发说明

### 钩子点
- `Widget_Archive->header`：在前台页面头部添加渲染脚本
- `Widget_Contents_Post_Edit->header`：在后台编辑页面头部添加渲染脚本

### 核心方法
- `renderHeader()`：根据配置渲染不同引擎的脚本
- `renderMathJax()`：渲染MathJax脚本
- `renderKaTeX()`：渲染KaTeX脚本
- `getDarkModeScript()`：获取深色模式脚本和样式

## 更新日志

### v1.0.0 (2023-12-23)
- 初始版本发布
- 支持MathJax和KaTeX两种渲染引擎
- 支持行内公式和块级公式
- 提供完整的配置选项
- 支持公式自动编号和引用
- 支持深色模式适配
- 增强KaTeX配置选项
- 支持多种渲染模式
- 支持自定义字体大小
- 支持多种错误处理方式
- 添加调试模式

## 许可证

本插件采用GPL v2许可证，详见LICENSE文件。

## 贡献

欢迎提交Issue和Pull Request来帮助改进插件！

---

**Enjoy writing with math formulas!** 📐📊📈