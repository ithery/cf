<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Jaspersoft Studio version 6.18.1.final using JasperReports Library version 6.18.1-9d75d1969e774d4f179fb3be8401e98a0e6d1611  -->
<jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://jasperreports.sourceforge.net/jasperreports http://jasperreports.sourceforge.net/xsd/jasperreport.xsd" name="groups" pageWidth="595" pageHeight="842" columnWidth="555" leftMargin="20" rightMargin="20" topMargin="20" bottomMargin="20" uuid="91b09141-a7ac-4a00-9867-4b7b8d085c5d">
    <pageHeader>
        <band height="30" splitType="Stretch">
            <staticText>
                <reportElement x="0" y="0" width="552" height="30" forecolor="#FF0000"/>

                <textElement textAlignment="Center">
                    <font size="16" isBold="true"/>
                </textElement>
                <text><![CDATA[Country List]]></text>
            </staticText>
        </band>
    </pageHeader>
    <columnHeader>
        <band height="50">
            <textField>
                <reportElement x="0" y="30" width="190" height="20">
                </reportElement>
                <box>
                    <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    <leftPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    <rightPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                </box>
                <textElement verticalAlignment="Middle">
                </textElement>
                <textFieldExpression><![CDATA["Country"]]></textFieldExpression>
            </textField>
            <textField>
                <reportElement x="190" y="30" width="190" height="20">
                </reportElement>
                <box>
                    <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    <leftPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    <rightPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                </box>
                <textElement verticalAlignment="Middle">
                </textElement>
                <textFieldExpression><![CDATA["Code"]]></textFieldExpression>
            </textField>
            <textField>
                <reportElement x="380" y="30" width="175" height="20">
                </reportElement>
                <box>
                    <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    <leftPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    <rightPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                </box>
                <textElement verticalAlignment="Middle">
                </textElement>
                <textFieldExpression><![CDATA["Continent"]]></textFieldExpression>
            </textField>
        </band>
    </columnHeader>
    <detail>
        <band height="20" splitType="Stretch">
            <textField textAdjust="StretchHeight">
                <reportElement x="0" y="0" width="190" height="20">
                </reportElement>
                <textElement verticalAlignment="Middle"/>
                <textFieldExpression><![CDATA[$F{name}]]></textFieldExpression>
            </textField>
            <textField>
                <reportElement x="190" y="0" width="190" height="20">
                </reportElement>
                <textElement verticalAlignment="Middle"/>
                <textFieldExpression><![CDATA[$F{code}]]></textFieldExpression>
            </textField>
            <textField>
                <reportElement x="380" y="0" width="175" height="20">
                </reportElement>
                <textElement verticalAlignment="Middle" textAlignment="Left"/>
                <textFieldExpression><![CDATA[$F{continent}]]></textFieldExpression>
            </textField>
        </band>
    </detail>

    <pageFooter>
        <band height="20" splitType="Stretch">
            <textField>
                <reportElement x="178" y="0" width="100" height="20" uuid="c42fbbae-2d5a-476c-bda0-2404726c7b30"/>
                <textElement textAlignment="Right">
                    <font size="8"/>
                </textElement>
                <textFieldExpression><![CDATA["Page " + $V{PAGE_NUMBER}]]></textFieldExpression>
            </textField>

            <textField pattern="d-M-yyyy h:mm:ss a">
                <reportElement x="0" y="0" width="178" height="20" uuid="ffd558c2-8b05-4669-8142-a7fdbc3ed46a"/>
                <textElement textAlignment="Left">
                    <font size="8"/>
                </textElement>
                <textFieldExpression><![CDATA["{{ date('Y-m-d H:i:s') }}"]]></textFieldExpression>
            </textField>
            <line>
                <reportElement x="0" y="0" width="552" height="1" uuid="1f1cab28-cf5b-4f72-954b-8e7f3ffd32e2"/>
                <graphicElement>
                    <pen lineWidth="0.5" lineStyle="Solid" lineColor="#9E9E9E"/>
                </graphicElement>
            </line>
        </band>
    </pageFooter>

</jasperReport>
