<?xml version="1.0" encoding="UTF-8"?>
<jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://jasperreports.sourceforge.net/jasperreports http://jasperreports.sourceforge.net/xsd/jasperreport.xsd" name="masterDetail" pageWidth="595" pageHeight="842" columnWidth="555" leftMargin="20" rightMargin="20" topMargin="20" bottomMargin="20" uuid="6f0a4c2e-3b7d-4f7a-9c1e-2d5b8a9e0c11">
    <variable name="salesTotal" class="java.lang.Double" calculation="Sum">
        <variableExpression><![CDATA[$F{subtotal}]]></variableExpression>
        <resetType>Group</resetType>
        <resetGroup>salesGroup</resetGroup>
    </variable>
    <variable name="grandTotal" class="java.lang.Double" calculation="Sum">
        <variableExpression><![CDATA[$F{subtotal}]]></variableExpression>
        <resetType>Report</resetType>
    </variable>
    <pageHeader>
        <band height="30" splitType="Stretch">
            <staticText>
                <reportElement x="0" y="0" width="555" height="30" forecolor="#FF0000"/>
                <textElement textAlignment="Center">
                    <font size="16" isBold="true"/>
                </textElement>
                <text><![CDATA[Sales Report - Master Detail]]></text>
            </staticText>
        </band>
    </pageHeader>
    <group name="salesGroup">
        <groupExpression><![CDATA[$F{invoice_no}]]></groupExpression>
        <groupHeader>
            <band height="56" splitType="Stretch">
                <textField>
                    <reportElement x="0" y="6" width="300" height="16"/>
                    <textElement verticalAlignment="Middle">
                        <font size="11" isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Invoice : " + $F{invoice_no}]]></textFieldExpression>
                </textField>
                <textField pattern="dd/MM/yyyy">
                    <reportElement x="380" y="6" width="175" height="16"/>
                    <textElement verticalAlignment="Middle" textAlignment="Right"/>
                    <textFieldExpression><![CDATA[$F{sales_date}]]></textFieldExpression>
                </textField>
                <textField>
                    <reportElement x="0" y="22" width="300" height="14"/>
                    <textElement verticalAlignment="Middle">
                        <font size="9"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Customer : " + $F{customer_name}]]></textFieldExpression>
                </textField>
                <textField>
                    <reportElement x="0" y="40" width="235" height="16"/>
                    <box>
                        <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                        <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    </box>
                    <textElement verticalAlignment="Middle">
                        <font isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Product"]]></textFieldExpression>
                </textField>
                <textField>
                    <reportElement x="235" y="40" width="60" height="16"/>
                    <box>
                        <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                        <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    </box>
                    <textElement verticalAlignment="Middle" textAlignment="Right">
                        <font isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Qty"]]></textFieldExpression>
                </textField>
                <textField>
                    <reportElement x="295" y="40" width="120" height="16"/>
                    <box>
                        <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                        <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    </box>
                    <textElement verticalAlignment="Middle" textAlignment="Right">
                        <font isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Price"]]></textFieldExpression>
                </textField>
                <textField>
                    <reportElement x="415" y="40" width="140" height="16"/>
                    <box>
                        <topPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                        <bottomPen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                    </box>
                    <textElement verticalAlignment="Middle" textAlignment="Right">
                        <font isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Subtotal"]]></textFieldExpression>
                </textField>
            </band>
        </groupHeader>
        <groupFooter>
            <band height="26" splitType="Stretch">
                <line>
                    <reportElement x="0" y="0" width="555" height="1"/>
                    <graphicElement>
                        <pen lineWidth="0.5" lineStyle="Solid" lineColor="#000000"/>
                    </graphicElement>
                </line>
                <textField>
                    <reportElement x="235" y="2" width="180" height="16"/>
                    <textElement verticalAlignment="Middle" textAlignment="Right">
                        <font isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA["Total"]]></textFieldExpression>
                </textField>
                <textField pattern="#,##0">
                    <reportElement x="415" y="2" width="140" height="16"/>
                    <textElement verticalAlignment="Middle" textAlignment="Right">
                        <font isBold="true"/>
                    </textElement>
                    <textFieldExpression><![CDATA[$V{salesTotal}]]></textFieldExpression>
                </textField>
            </band>
        </groupFooter>
    </group>
    <detail>
        <band height="16" splitType="Stretch">
            <textField textAdjust="StretchHeight">
                <reportElement x="0" y="0" width="235" height="16"/>
                <textElement verticalAlignment="Middle"/>
                <textFieldExpression><![CDATA[$F{product_name}]]></textFieldExpression>
            </textField>
            <textField>
                <reportElement x="235" y="0" width="60" height="16"/>
                <textElement verticalAlignment="Middle" textAlignment="Right"/>
                <textFieldExpression><![CDATA[$F{qty}]]></textFieldExpression>
            </textField>
            <textField pattern="#,##0">
                <reportElement x="295" y="0" width="120" height="16"/>
                <textElement verticalAlignment="Middle" textAlignment="Right"/>
                <textFieldExpression><![CDATA[$F{price}]]></textFieldExpression>
            </textField>
            <textField pattern="#,##0">
                <reportElement x="415" y="0" width="140" height="16"/>
                <textElement verticalAlignment="Middle" textAlignment="Right"/>
                <textFieldExpression><![CDATA[$F{subtotal}]]></textFieldExpression>
            </textField>
        </band>
    </detail>
    <summary>
        <band height="28" splitType="Stretch">
            <line>
                <reportElement x="0" y="2" width="555" height="1"/>
                <graphicElement>
                    <pen lineWidth="1.0" lineStyle="Solid" lineColor="#000000"/>
                </graphicElement>
            </line>
            <textField>
                <reportElement x="235" y="6" width="180" height="18"/>
                <textElement verticalAlignment="Middle" textAlignment="Right">
                    <font size="11" isBold="true"/>
                </textElement>
                <textFieldExpression><![CDATA["Grand Total"]]></textFieldExpression>
            </textField>
            <textField pattern="#,##0">
                <reportElement x="415" y="6" width="140" height="18"/>
                <textElement verticalAlignment="Middle" textAlignment="Right">
                    <font size="11" isBold="true"/>
                </textElement>
                <textFieldExpression><![CDATA[$V{grandTotal}]]></textFieldExpression>
            </textField>
        </band>
    </summary>
    <pageFooter>
        <band height="20" splitType="Stretch">
            <textField>
                <reportElement x="178" y="0" width="100" height="20"/>
                <textElement textAlignment="Right">
                    <font size="8"/>
                </textElement>
                <textFieldExpression><![CDATA["Page " + $V{PAGE_NUMBER}]]></textFieldExpression>
            </textField>
            <textField>
                <reportElement x="0" y="0" width="178" height="20"/>
                <textElement textAlignment="Left">
                    <font size="8"/>
                </textElement>
                <textFieldExpression><![CDATA["{{ date('Y-m-d H:i:s') }}"]]></textFieldExpression>
            </textField>
            <line>
                <reportElement x="0" y="0" width="555" height="1"/>
                <graphicElement>
                    <pen lineWidth="0.5" lineStyle="Solid" lineColor="#9E9E9E"/>
                </graphicElement>
            </line>
        </band>
    </pageFooter>
</jasperReport>
