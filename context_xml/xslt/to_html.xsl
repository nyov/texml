<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:exsl="http://exslt.org/common"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    extension-element-prefixes="exsl"
    version="1.1"
    exclude-result-prefixes = "exsl tei "
>
    <xsl:output method="xml" encoding="ASCII"/>

    <!--variables-->

    <!--where to put the pages-->
    <!--
    <xsl:variable name="output-dir" select="'/home/paul/Documents/context/html/'"/>
    -->
    <xsl:variable name="output-dir" select="'./'"/>

    <!--html suffix-->
    <xsl:variable name="html-suffix" select="'.html'"/>

    <!--title for index page-->
    <xsl:variable name="index-title" select="'index'"/>

    <!--is it a draft?-->
    <xsl:variable name="draft" select="'true'"/>


    <!--number of total pages-->
    <xsl:variable name="total-pages" >
        <xsl:value-of select = "count(//tei:body/tei:div)"/>
    </xsl:variable>

    <!--root-->

    <xsl:template match = "/">
        <xsl:call-template name="make-index"/>
        <xsl:apply-templates/>
    </xsl:template>


    <!--make toc for index page-->
    <xsl:template name = "make-index">
        <xsl:variable name="output">
            <xsl:value-of select="$output-dir"/>
            <xsl:text>contents</xsl:text>
            <xsl:value-of select = "$html-suffix"/>
        </xsl:variable>
        <xsl:document href="{$output}" method="xml">
            <html>
                <head>
                    <title><xsl:value-of select = "contents"/></title>
                </head>
                <xsl:element name="link">
                    <xsl:attribute name="rel">
                        <xsl:text>stylesheet</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:text>text/css</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="href">
                        <xsl:text>styles.css</xsl:text>
                    </xsl:attribute>
                </xsl:element>
                <body>
                    <h1 class="index-head">Contents</h1>
                    <xsl:element name="div">
                        <xsl:attribute name="class">
                            <xsl:text>index-toc</xsl:text>
                        </xsl:attribute>
                        <xsl:for-each select = "/tei:TEI/tei:text/tei:body/tei:div[not(@id='intro-id')]">
                            <xsl:element name="p">
                                <xsl:attribute name="class">
                                    <xsl:text>toc-external</xsl:text>
                                </xsl:attribute>
                                <xsl:element name="a">
                                    <xsl:attribute name="href">
                                        <xsl:text>page</xsl:text>
                                        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
                                        <xsl:value-of select = "$html-suffix"/>
                                    </xsl:attribute>
                                    <xsl:apply-templates select="tei:head" mode="toc-index"/>
                                </xsl:element>
                            </xsl:element>
                            <xsl:apply-templates select="tei:div" mode="toc-index"/>
                        </xsl:for-each>
                    </xsl:element>
                    <xsl:call-template name="page-links"/>
                    <xsl:call-template name="logo"/>
                </body>
            </html>
        </xsl:document>
    </xsl:template>


    <!--div for contentx-->
    <xsl:template match="tei:div" mode="toc-index">
        <xsl:element name="p">
            <xsl:attribute name="class">
                <xsl:text>index-toc-level</xsl:text>
                <xsl:value-of select="count(ancestor::tei:div)"/>
            </xsl:attribute>
            <xsl:element name="a">
                <xsl:attribute name="href">
                    <xsl:text>page</xsl:text>
                    <xsl:for-each select="ancestor::tei:div[last()]">
                        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
                    </xsl:for-each>
                    <xsl:value-of select = "$html-suffix"/>
                    <xsl:text>#</xsl:text>
                    <xsl:value-of select="generate-id(.)"/>
                </xsl:attribute>
                <xsl:apply-templates select="tei:head" mode="toc"/>
            </xsl:element>
        </xsl:element>
        <xsl:apply-templates select="tei:div" mode="toc-index"/>
    </xsl:template>

    <xsl:template match="tei:TEI">
        <xsl:apply-templates/>
    </xsl:template>

    <!--teiHeader-->
    <!--for now, do nothing with-->
    <xsl:template match ="tei:teiHeader"/>

    <!--text-->
    <xsl:template match="tei:text">
        <xsl:apply-templates/>
    </xsl:template>

    <!--body-->
    <xsl:template match="tei:body">
        <xsl:apply-templates/>
    </xsl:template>



    <!--main div which we break up into pages-->
  <xsl:template match="tei:body/tei:div">
    <xsl:variable name="output">
        <xsl:value-of select="$output-dir"/>
        <xsl:text>page</xsl:text>
        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
        <xsl:value-of select = "$html-suffix"/>
    </xsl:variable>
    <xsl:document href="{$output}" method="xml">
        <html>
            <head>
                <title><xsl:value-of select="tei:head"/></title>
                <xsl:element name="link">
                    <xsl:attribute name="rel">
                        <xsl:text>stylesheet</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:text>text/css</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="href">
                        <xsl:text>styles.css</xsl:text>
                    </xsl:attribute>
                </xsl:element>
            </head>
            <body>
                <xsl:apply-templates/>
                <xsl:call-template name="copyright"/>
                <xsl:call-template name="page-links"/>
                <xsl:call-template name="logo"/>
                <xsl:apply-templates select="tei:bibl[last()]" mode="last-updated"/>
            </body>
        </html>
    </xsl:document>
  </xsl:template>

  <!--INDEX-->
  <xsl:template match="tei:body/tei:div[@id='intro-id']">
    <xsl:variable name="output">
        <xsl:text>index</xsl:text>
        <xsl:value-of select = "$html-suffix"/>
    </xsl:variable>
    <xsl:document href="{$output}" method="xml">
        <html>
            <head>
                <title>index.html</title>
                <xsl:element name="link">
                    <xsl:attribute name="rel">
                        <xsl:text>stylesheet</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:text>text/css</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="href">
                        <xsl:text>styles.css</xsl:text>
                    </xsl:attribute>
                </xsl:element>
            </head>
            <body>
                <h1 class ="main-title">Welcome to context-xml</h1>
                <p>
                    <a href="contents.html">Contents of Documentation</a>
                </p>
                <xsl:apply-templates/>
                <p>
        <b>Author: </b>Paul Tremblay <br/><b>email: </b>phthenry [at] {iglou} [dot] com
        </p>
        <xsl:element name="p">
            <b>Last Updated: </b>
            <xsl:call-template name="site-last-updated"/>
        </xsl:element>

        <p>
        <a href="http://sourceforge.net">
            <img src="http://sourceforge.net/sflogo.php?group_id=102261&amp;type=5" 
            width="210" 
            height="62" 
            border="0" 
            alt="SourceForge.net Logo" />
        </a>
            
        </p>
            </body>
        </html>
    </xsl:document>
  </xsl:template>

  <xsl:template name="site-last-updated">
      <xsl:variable name="update-string">
          <xsl:for-each select = "//tei:bibl/tei:date/@value">
              <xsl:value-of select="."/>
              <xsl:text>;</xsl:text>
          </xsl:for-each>
      </xsl:variable>
      <xsl:variable name="last-updated">
          <xsl:call-template name="munge-update-string">
              <xsl:with-param name="the-string" select = "$update-string"/>
          </xsl:call-template>
      </xsl:variable>
      <xsl:variable name="date">
         <xsl:value-of select="substring($last-updated, 1,4)"/> 
        <xsl:text>-</xsl:text>
         <xsl:value-of select="substring($last-updated, 5,2)"/> 
         <xsl:text>-</xsl:text>
         <xsl:value-of select="substring($last-updated, 7,2)"/> 
      </xsl:variable>
      <xsl:value-of select="$date"/>
  </xsl:template>

  <xsl:template name="munge-update-string">
    <xsl:param name="the-string"/>
    <xsl:param name="most-recent" select="number('00000000')"/>
    <xsl:variable name="current-date">
        <xsl:variable name="temp"  select = "substring-before($the-string, ';')"/>
        <xsl:value-of select="number(translate($temp, '-',''))"/>
    </xsl:variable>
    <xsl:choose>
        <xsl:when test = "normalize-space($current-date)">
            <xsl:variable name="this-most-recent">
                <xsl:choose>
                    <xsl:when test="$current-date &gt; $most-recent">
                        <xsl:value-of select = "$current-date"/>
                    </xsl:when> 
                    <xsl:otherwise>
                        <xsl:value-of select = "$most-recent"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:variable name="next-string">
                <xsl:value-of select= "substring-after($the-string, ';')"/>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="normalize-space($next-string)">
                    <xsl:call-template name="munge-update-string">
                        <xsl:with-param name="the-string" select = "$next-string"/>
                        <xsl:with-param name="most-recent" select = "$this-most-recent"/>
                    </xsl:call-template>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select= "$this-most-recent"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select= "$most-recent"/>
        </xsl:otherwise>
    </xsl:choose>
      
  </xsl:template>

  <xsl:template match="tei:body/tei:div[@id='intro-id']/tei:head" priority="100"/>

  <xsl:template match="tei:p[@rend='status']"/>

<!--END INDEX-->

  <!--get rid of bug pages-->
  <xsl:template match="tei:div[@type='unsure']" priority="10"/>

  <!--page-links template-->
  <xsl:template name="page-links">
    <xsl:variable name="page-number">
        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
    </xsl:variable>
    <xsl:element name="p">
        <xsl:element name="a">
            <xsl:attribute name="href">
                <xsl:text>index</xsl:text>
                <xsl:value-of select="$html-suffix"/>
            </xsl:attribute>
            <xsl:text>home | </xsl:text>
        </xsl:element>
        <xsl:element name="a">
            <xsl:attribute name="href">
                <xsl:text>contents</xsl:text>
                <xsl:value-of select="$html-suffix"/>
            </xsl:attribute>
            <xsl:text>contents | </xsl:text>
        </xsl:element>
        <xsl:choose>
            <xsl:when test="$page-number = '1'">
                <!--
                <xsl:text>previous | </xsl:text>
                -->
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="a">
                    <xsl:attribute name="href">
                        <xsl:text>page</xsl:text>
                        <xsl:value-of select="$page-number - 1"/>
                        <xsl:value-of select = "$html-suffix"/>
                    </xsl:attribute>
                    <xsl:text>previous | </xsl:text>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test = "$page-number = $total-pages">
                <!--
                <xsl:text>next</xsl:text>
                -->
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="a">
                    <xsl:attribute name="href">
                        <xsl:text>page</xsl:text>
                        <xsl:value-of select = "$page-number + 1"/>
                        <xsl:value-of select = "$html-suffix"/>
                    </xsl:attribute>
                    <xsl:text>next</xsl:text>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:element>
      
  </xsl:template>

  <!--logo (for sourceforge)-->
  <xsl:template name="logo">
  <p>
  <a href="http://sourceforge.net">
    <img src="http://sourceforge.net/sflogo.php?group_id=102261&amp;type=1" 
        width="88" 
        height="31" 
        border="0" 
        alt="SourceForge.net Logo"/>
    </a>
  </p>
  </xsl:template>
  <!--

  <A href="http://sourceforge.net"> <IMG src="http://sourceforge.net/sflogo.php?group_id=102261&amp;type=5" width="210" height="62" border="0" alt="SourceForge.net Logo" /></A>
  -->

  <!--copyright-->
  <xsl:template name="copyright">
  <p>
 copyright 2005 Paul Henry Tremblay
      
  </p>
  <p>
      License: GPL
  </p>
      
  </xsl:template>

  <xsl:template match="tei:body/tei:div/tei:head">
      <xsl:element name="h2">
          <xsl:attribute name="class">
              <xsl:text>page-head</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--but not for the index-->
  <xsl:template match="tei:body/tei:div/tei:head" mode="toc-index">
      <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="tei:head" mode = "toc">
      <xsl:apply-templates/>
  </xsl:template>

  <!--for now, get rid of all the revision history for each main division-->
  <xsl:template match="tei:bibl"/>

  <xsl:template match="tei:bibl" mode="last-updated">
    <xsl:element name="p">
        <b>
        <xsl:text>last updated: </xsl:text>
        </b>
        <xsl:value-of select="tei:date/@value"/>
    </xsl:element>
  </xsl:template>

  <!--div 2 level-->
  <xsl:template match="tei:body/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>2</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 2 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:head">
      <xsl:element name="h3">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>2</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div 3 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>3</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 3 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:head">
      <xsl:element name="h4">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>3</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>


  <!--div 4 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>4</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 4 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div/tei:head">
      <xsl:element name="h5">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>4</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div 5 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>5</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 5 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div/tei:div/tei:head">
      <xsl:element name="h6">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>5</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--divGen-->
  <!--divGen will generally be used to reference external example documents-->
  <!--for now, am not sure what to do with these documents-->
  <xsl:template match="tei:divGen[@type='simple1_tex']"/>
  <xsl:template match="tei:divGen[@type='simple1_texml']"/>
  <xsl:template match="tei:divGen[@type='page_setup1_tex']"/>
  <xsl:template match="tei:divGen[@type='page_setup1_texml']"/>


  <!--generic p-->
  <xsl:template match = "tei:p">
      <xsl:element name="p">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--tables-->
  <xsl:template match="tei:table">
      <xsl:element name="table">
        <xsl:attribute name="id">
            <xsl:value-of select="@id"/>
        </xsl:attribute>
        <xsl:choose>
            <xsl:when test="tei:row[@role='label']">
                <xsl:element name="thead">
                    <xsl:apply-templates select="tei:row[@role='label']" mode="table-head"/>
                </xsl:element>
            </xsl:when>
        </xsl:choose>
        <xsl:element name="tbody">
            <xsl:apply-templates/>
        </xsl:element>
      </xsl:element>
  </xsl:template>

  <!--row with label-->
  <!--get rid of since it is alreay processed in thead-->
  <xsl:template match="tei:row[@role='label']"/>

  <!--actually process the header row-->
  <xsl:template match="tei:row[@role='label']" mode="table-head">
      <xsl:element name="tr">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--row-->
  <xsl:template match="tei:row">
      <xsl:element name="tr">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--cell-->
  <xsl:template match="tei:cell">
      <xsl:element name="td">
        <xsl:if test="not(following-sibling::tei:cell)">
            <xsl:attribute name="class">
                <xsl:text>last</xsl:text>
            </xsl:attribute>
        </xsl:if>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--table head-->
  <xsl:template match="tei:table/tei:head">
      <caption align="bottom">
          <xsl:apply-templates/>
      </caption>
  </xsl:template>

  <!--block code-->
  <xsl:template match="tei:eg[@rend='context-block-code']|tei:eg[@rend='xml']">
    <xsl:element name="div">
        <xsl:attribute name="class">
            <xsl:value-of select="@rend"/>
        </xsl:attribute>
          <xsl:element name="pre">
              <xsl:apply-templates/>
          </xsl:element>
    </xsl:element>
  </xsl:template>

  <!--lists-->

  <!--ordered list-->
  <xsl:template match="tei:list[@type='ordered']">
      <xsl:element name="ol">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--undorederd list-->
  <xsl:template match="tei:list[@type='unordered']">
    <xsl:apply-templates select="tei:head" mode="list"/>
      <xsl:element name="ul">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--item in list-->
  <xsl:template match="tei:item">
      <xsl:element name="li">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--head in list-->
  <xsl:template match="tei:list/tei:head"/>
  <xsl:template match="tei:list/tei:head" mode="list">
    <xsl:choose>
        <xsl:when test="normalize-space(.) = 'Tips'">
            <xsl:element name="h3">
                <xsl:attribute name="class">
                    <xsl:text>list-head</xsl:text>
                </xsl:attribute>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:when>
    </xsl:choose>
  </xsl:template>

  <!--figure-->
  <xsl:template match="tei:figure">
      <xsl:variable name="path">
          <xsl:value-of select = "concat('png_images/', substring-before(@url, '.'), '.png')"/>
      </xsl:variable>
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>img</xsl:text>
          </xsl:attribute>
          <xsl:element name="img">
              <xsl:attribute name="src">
                  <xsl:value-of select="$path"/>
              </xsl:attribute>
              <xsl:attribute name="alt">
                  <xsl:value-of select="tei:head"/>
              </xsl:attribute>
              <xsl:apply-templates/>
          </xsl:element>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:figure/tei:head">
      <xsl:element name="p">
        <xsl:attribute name="class">
            <xsl:text>figure-caption</xsl:text>
        </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>
    
  <!--inline-->


  <!--xref-->
  <xsl:template match="tei:xref[@url]">
      <xsl:element name="a">
          <xsl:attribute name="href">
              <xsl:value-of select="@url"/>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match = "tei:emph[@rend='italic']">
      <xsl:element name="i">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:emph[@rend='bold']">
      <xsl:element name="b">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='code']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='latex-name']">
      <xsl:text>LaTeX</xsl:text>
  </xsl:template>

  <xsl:template match="tei:seg[@type='tex-name']">
      <xsl:text>TeX</xsl:text>
  </xsl:template>

  <xsl:template match="tei:seg[@type='context-name']">
      <xsl:text>ConTeXt</xsl:text>
  </xsl:template>

  <xsl:template match="tei:seg[@type='context-command']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='property']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:eg[@rend='command']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:eg[@rend='context-code']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='option']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>


  <xsl:template match="tei:seg[@type='command']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="*">
      <xsl:if test="$draft='true'">
          <h1><xsl:value-of select="name(.)"/></h1>
          <xsl:message>
              <xsl:text>no value for </xsl:text>
              <xsl:value-of select="name(.)"/>
          </xsl:message>
      </xsl:if>
      <xsl:apply-templates/>
  </xsl:template>

</xsl:stylesheet>
