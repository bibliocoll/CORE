<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xlink="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:amf="http://amf.openlib.org">
    <xsl:output method="xml" indent="yes" encoding="utf-8"/>
    <xsl:param name="institution">My University</xsl:param>
    <xsl:param name="collection">RePEc Test</xsl:param>

<xsl:template match="/">
<add>
<!--
<xsl:apply-templates select="document('/usr/local/vufind/DATA/repec/ftp.repec.org/opt/amf/RePEc/zbw/hwwirp/138.amf.xml')//amf:text"/>
-->
<!--
<xsl:apply-templates select="document(concat('/usr/local/vufind/DATA/repec/ftp.repec.org/opt/amf/RePEc/zbw/hwwirp/','xml'))//amf:text"/>
-->
<xsl:apply-templates/>
        </add>
</xsl:template>


    <xsl:template match="amf:text">
         
            <doc>
                <!-- ID -->
                <!-- Important: This relies on an <identifier> tag being injected by the OAI-PMH harvester. -->
                <field name="id">
		  <xsl:value-of select="translate(current()/@id,':','')"/>
		</field>

                <!-- RECORDTYPE -->
                <field name="recordtype">Article</field>

                <!-- FULLRECORD -->
                <!-- disabled for now; records are so large that they cause memory problems!
                <field name="fullrecord">
                    <xsl:copy-of select="php:function('VuFind::xmlAsText', /amf:record)"/>
                </field>
                  -->

                <!-- ALLFIELDS -->
                <field name="allfields">
                    <xsl:value-of select="normalize-space(string(/amf:text))"/>
                </field>

                <!-- INSTITUTION -->
                <field name="institution">
                    <xsl:value-of select="$institution" />
                </field>

                <!-- COLLECTION -->
                <field name="collection">
                    <xsl:value-of select="$collection" />
                </field>

                <!-- LANGUAGE -->
                <!-- TODO: add language support; in practice, there don't seem to be
                     many records with <language> tags in them.  If we encounter any,
                     the code below is partially complete, but we probably need to
                     build a new language map for ISO 639-2b, which is the standard
                     specified by the DOAJ XML schema.
                <xsl:if test="/amf:record/amf:language">
                    <xsl:for-each select="/amf:record/amf:language">
                        <xsl:if test="string-length() > 0">
                            <field name="language">
                                <xsl:value-of select="php:function('VuFind::mapString', normalize-space(string(.)), 'language_map_iso639-1.properties')"/>
                            </field>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:if>
                -->

                <!-- FORMAT -->
                <field name="format">Article</field>

                <!-- AUTHOR -->
                <xsl:if test="//amf:hasauthor">
                    <xsl:for-each select="//amf:hasauthor">
                        <xsl:if test="normalize-space()">
                            <!-- author is not a multi-valued field, so we'll put
                                 first value there and subsequent values in author2.
                             -->
                            <xsl:if test="position()=1">
                                <field name="author">
                                    <xsl:value-of select="amf:person/amf:name[normalize-space()]"/>
                                </field>
                                <field name="author-letter">
                                    <xsl:value-of select="normalize-space()"/>
                                </field>
                            </xsl:if>
                            <xsl:if test="position()>1">
                                <field name="author2">
                                    <xsl:value-of select="amf:person/amf:name[normalize-space()]"/>
                                </field>
                            </xsl:if>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:if>

                <!-- TITLE -->
                <xsl:if test="//amf:title[normalize-space()]">
                    <field name="title">
                        <xsl:value-of select="//amf:title[normalize-space()]"/>
                    </field>
                    <field name="title_short">
                        <xsl:value-of select="//amf:title[normalize-space()]"/>
                    </field>
                    <field name="title_full">
                        <xsl:value-of select="//amf:title[normalize-space()]"/>
                    </field>
                    <field name="title_sort">
                        <!-- <xsl:value-of select="php:function('VuFind::stripArticles', string(//amf:title[normalize-space()]))"/> -->
                    </field>
                </xsl:if>

                <!-- PUBLISHER -->
                <xsl:if test="//amf:haspublisher[normalize-space()]">
                    <field name="publisher">
                        <xsl:value-of select="//amf:haspublisher[normalize-space()]"/>
                    </field>
                </xsl:if>

                 <!-- SERIES -->
                <!-- <xsl:if test="//amf:journalTitle[normalize-space()]"> -->
                <!--     <field name="series"> -->
                <!--         <xsl:value-of select="//amf:journalTitle[normalize-space()]"/> -->
                <!--     </field> -->
                <!-- </xsl:if> -->

                 <!-- ISSN  -->
                <xsl:if test="//amf:issn[normalize-space()]">
                    <field name="issn">
                        <xsl:value-of select="//amf:issn[normalize-space()]"/>
                    </field>
                </xsl:if>

                <!-- ISSN  -->
                <xsl:if test="//amf:journalidentifier[normalize-space()]">
                    <field name="issn">
                        <xsl:value-of select="//amf:journalidentifier[normalize-space()]"/>
                    </field>
                </xsl:if>

                <!-- PUBLISHDATE -->
                <xsl:if test="//amf:date">
                    <field name="publishDate">
                        <xsl:value-of select="//amf:date"/>
                    </field>
                </xsl:if>

                <!-- DESCRIPTION -->
                <xsl:if test="//amf:abstract">
                    <field name="description">
                        <xsl:value-of select="//amf:abstract" />
                    </field>
                </xsl:if>

                <!-- SUBJECT -->
                <xsl:if test="//amf:keywords">
                    <xsl:for-each select="//amf:keywords/keyword">
                        <xsl:if test="string-length() > 0">
                            <field name="topic">
                                <xsl:value-of select="//amf:keywords/keyword[normalize-space()]"/>
                            </field>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:if>

                <!-- URL -->
                <xsl:if test="//amf:file/amf:url">
                    <field name="url">
                        <xsl:value-of select="//amf:file/amf:url[normalize-space()]"/>
                    </field>
                </xsl:if>
            </doc>

    </xsl:template>
</xsl:stylesheet>
