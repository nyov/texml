<!-- 
Text Encoding Initiative Consortium XSLT stylesheet family
$Date: 2005-04-02 23:49:26 $, $Revision: 1.1 $, $Author: paultremblay $

XSL stylesheet to format TEI XML documents to HTML or XSL FO

 
Copyright 1999-2005 Sebastian Rahtz / Text Encoding Initiative Consortium
                                              
    This is an XSLT stylesheet for transforming TEI (version P5) XML documents

    Version 4.3.3. Date Tue Feb  8 22:21:09 GMT 2005

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    The author may be contacted via the e-mail address

    sebastian.rahtz@computing-services.oxford.ac.uk--> 
<xsl:stylesheet
   xmlns:a="http://relaxng.org/ns/compatibility/annotations/1.0"
   xmlns:edate="http://exslt.org/dates-and-times"
   xmlns:estr="http://exslt.org/strings"
   xmlns:exsl="http://exslt.org/common"
   xmlns:fo="http://www.w3.org/1999/XSL/Format"
   xmlns:local="http://www.pantor.com/ns/local"
   xmlns:rng="http://relaxng.org/ns/structure/1.0"
   xmlns:tei="http://www.tei-c.org/ns/1.0"
   xmlns:teix="http://www.tei-c.org/ns/Examples"
   extension-element-prefixes="exsl estr edate"
   exclude-result-prefixes="exsl estr edate a fo local rng tei teix"
   xmlns:html="http://www.w3.org/1999/xhtml"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0">
  
<xsl:import href="/usr/share/xsl/tei-xsl-4.3.3/p5/html/teihtml.xsl"/>


  <xsl:param name="ID"></xsl:param>
  <xsl:param name="REQUEST"/>
  <xsl:param name="STDOUT">true</xsl:param>
  <xsl:param name="alignNavigationPanel">right</xsl:param>
  <xsl:param name="autoToc">true</xsl:param>
  <xsl:param name="bottomNavigationPanel">true</xsl:param>
  <xsl:param name="cellAlign">left</xsl:param>
  <xsl:param name="class_ptr">ptr</xsl:param>
  <xsl:param name="class_quicklink">quicklink</xsl:param>
  <xsl:param name="class_ref">ref</xsl:param>
  <xsl:param name="class_subtoc">subtoc</xsl:param>
  <xsl:param name="class_xptr">xptr</xsl:param>
  <xsl:param name="class_xref">xref</xsl:param>
  <xsl:param name="cssFile">/stylesheets/tei-oucs.css</xsl:param>
  <xsl:param name="cssSecondaryFile"></xsl:param>
  <xsl:param name="css_override"></xsl:param>
  <xsl:param name="divOffset">2</xsl:param>
  <xsl:param name="downPicture">http://www.oucs.ox.ac.uk/images/down.gif</xsl:param>
  <xsl:param name="feedbackURL">mailto:feedback</xsl:param>
  <xsl:param name="fontURL">span</xsl:param>
  <xsl:param name="footnoteFile"/>
  <xsl:param name="frameAlternateURL"/>
  <xsl:param name="frameCols">200,*</xsl:param>
  <xsl:param name="generateParagraphIDs">true</xsl:param>
  <xsl:param name="graphicsPrefix"/>
  <xsl:param name="graphicsSuffix">.png</xsl:param>
  <xsl:param name="htmlTitlePrefix"></xsl:param>
  <xsl:param name="inputName"/>
  <xsl:param name="layout">Simple</xsl:param><!-- Simple, CSS, Table, or Frames -->
  <xsl:param name="linkPanel">true</xsl:param>
  <xsl:param name="linksWidth">15%</xsl:param>
  <xsl:param name="makeFrames"/>
  <xsl:param name="makePageTable"/>
  <xsl:param name="makingSlides"/>
  <xsl:param name="noframeWords">No Frames</xsl:param>
  <xsl:param name="numberParagraphs"></xsl:param>
  <xsl:param name="outputDir"/>
  <xsl:param name="outputEncoding">utf8</xsl:param>
  <xsl:param name="pageLayout">Simple</xsl:param>
  <xsl:param name="postQuote">&#x2019;</xsl:param>
  <xsl:param name="preQuote">&#x2018;</xsl:param>
  <xsl:param name="preferred_bgcolor">#000000</xsl:param>
  <xsl:param name="preferred_color">#FFFF00</xsl:param>
  <xsl:param name="preferred_font">Helvetica</xsl:param>
  <xsl:param name="preferred_linkcolor">#0000FF</xsl:param>
  <xsl:param name="preferred_size">130%</xsl:param>
  <xsl:param name="rawXML"/>
  <xsl:param name="rendSeparator" select="';'"/>
  <xsl:param name="sectionTopLink"/>
  <xsl:param name="sectionUpLink"/>
  <xsl:param name="showFigures">true</xsl:param>
  <xsl:param name="showTitleAuthor"/>
  <xsl:param name="splitBackmatter">true</xsl:param>
  <xsl:param name="splitFrontmatter">true</xsl:param>

  <xsl:param name="splitLevel">-1</xsl:param>
  <xsl:param name="subTocDepth">5</xsl:param>

  <xsl:param name="tableAlign">left</xsl:param>
  <xsl:param name="teixslHome">http://www.oucs.ox.ac.uk/stylesheets/</xsl:param> 
  <xsl:param name="tocBack">true</xsl:param>
  <xsl:param name="tocDepth">5</xsl:param>
  <xsl:param name="tocFront">true</xsl:param>
  <xsl:param name="topNavigationPanel">true</xsl:param>
  <xsl:param name="urlChunkPrefix">?ID=</xsl:param>
  <xsl:param name="useHeaderFrontMatter"/>
  <xsl:param name="useIDs">true</xsl:param>
  <xsl:param name="verbose"/>
  



<xsl:param name="teiP4Compat">false</xsl:param>
<xsl:param name="processor">
   <xsl:value-of select="system-property('xsl:vendor')"/>
</xsl:param>

<!-- common parameters -->
<xsl:param name="baseURL">http://www.tei-c.org</xsl:param>
<xsl:param name="homeLabel">Home</xsl:param>
<xsl:param name="homeURL">http://www.tei-c.org/</xsl:param>
<xsl:param name="homeWords">TEI</xsl:param>
<xsl:param name="department"/>
<xsl:param name="parentURL">http://www.tei-c.org/</xsl:param>
<xsl:param name="parentWords"/>
<xsl:param name="searchURL">http://www.google.com</xsl:param>
<xsl:param name="institution"/>
<xsl:template name="feedbackWords">Feedback</xsl:template>
<xsl:param name="class_toc">toc</xsl:param>

<!-- numbering -->
<!-- fill in these with a valid number format (eg A.1) if needed-->
<xsl:param name="useHeaderFrontMatter"/>
<xsl:param name="numberSpacer"><xsl:text> </xsl:text></xsl:param><!--&#160;-->
<xsl:param name="headingNumberSuffix">.<xsl:value-of 
select="$numberSpacer"/></xsl:param>
<xsl:param name="numberFigures">true</xsl:param>
<xsl:param name="numberFrontFigures">false</xsl:param>
<xsl:param name="numberBackFigures">false</xsl:param>
<xsl:param name="numberTables">true</xsl:param>
<xsl:param name="numberFrontTables">true</xsl:param>
<xsl:param name="numberBackTables">true</xsl:param>
<xsl:param name="numberHeadings">true</xsl:param>
<xsl:param name="numberHeadingsDepth">9</xsl:param>
<xsl:param name="prenumberedHeadings"></xsl:param>
<xsl:param name="numberBackHeadings">A.1</xsl:param>
<xsl:param name="numberFrontHeadings"></xsl:param>
<xsl:param name="minimalCrossRef"/>
<xsl:param name="autoHead"></xsl:param>


  
  
  <xsl:template name="singleFileLabel">For Printing</xsl:template>
  
  <xsl:template name="searchbox"/>
  
  <xsl:template name="copyrightStatement"/>  
  <xsl:template name="logoPicture"/>
  <!--
  <xsl:template name="logoPicture">
    <a target="_top" href="http://www.ox.ac.uk/">
      <img border="0" width="78" height="94"
	   src="/images/ncrest.gif"
	   alt="Oxford University"/>
    </a>
  </xsl:template>
  -->
  
  <xsl:template name="logoFramePicture"/>
  <!--
  <xsl:template name="logoFramePicture">
    <a class="framelogo" target="_top" href="http://www.ox.ac.uk">
      <img src="http://www.oucs.ox.ac.uk/images/newcrest902.gif"
	   vspace="5" width="90" height="107" border="0"
	   alt="University Of Oxford"/></a>
  </xsl:template>
  -->



</xsl:stylesheet>
