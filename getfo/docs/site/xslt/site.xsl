<x:stylesheet version="1.0" xmlns:x="http://www.w3.org/1999/XSL/Transform">
<!-- -->
<x:output method="html" encoding="iso-8859-1"/>

<x:variable name="docid" select="/html/@id"/>

<x:template match="node()|@*">
	<x:copy>
		<x:apply-templates select="node()|@*"/>
	</x:copy>
</x:template>

<x:template match="body">
	<body>
		<x:apply-templates select="document('../inc/menu.xml')"/>
		<x:apply-templates select="node()|@*"/>
		<x:apply-templates select="document('../inc/footer.xml')"/>
	</body>
</x:template>

<x:template match="menu">
	<x:apply-templates select="node()|@*"/>
	<hr />
</x:template>

<x:template match="footer">
	<x:apply-templates select="node()|@*"/>
</x:template>

<x:template match="menuitem">
	<x:text>[</x:text>
	<x:choose>
		<x:when test="@id=$docid">
			<x:value-of select="."/>
		</x:when>
		<x:otherwise>
			<a>
				<x:attribute name="href">
					<x:choose>
						<x:when test="@id">
							<x:if test="contains($docid,'.')">
								<x:text>../</x:text>
							</x:if>
							<x:value-of select="concat(translate(@id,'.','/'),'.html')"/>
						</x:when>
						<x:otherwise>
							<x:value-of select="@fileref"/>
						</x:otherwise>
					</x:choose>
				</x:attribute>
				<x:value-of select="."/>
			</a>
		</x:otherwise>
	</x:choose>
	<x:text>]</x:text>	
</x:template>

</x:stylesheet>
