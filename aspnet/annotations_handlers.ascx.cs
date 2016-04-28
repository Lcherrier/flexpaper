using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using lib;
using System.Data.SqlClient;

public partial class annotations_handlers : System.Web.UI.UserControl
{
    protected Config configManager;
    protected SqlConnection conn;

    protected void Page_Load(object sender, EventArgs e)
    {
        configManager = new Config(Server.MapPath(VirtualPathUtility.GetDirectory(Request.Path)));

        if (configManager.getConfig("sql.verified") == "true")
        {
            conn = new SqlConnection(String.Format(configManager.getConfig("sql.connectionstring")));
        }
    }

    protected static string SqlEscape(string str)
    {
        return Regex.Replace(str, @"[\x00'""\b\n\r\t\cZ\\%_]",
            delegate(Match match)
            {
                string v = match.Value;
                switch (v)
                {
                    case "\x00":            // ASCII NUL (0x00) character
                        return "\\0";
                    case "\b":              // BACKSPACE character
                        return "\\b";
                    case "\n":              // NEWLINE (linefeed) character
                        return "\\n";
                    case "\r":              // CARRIAGE RETURN character
                        return "\\r";
                    case "\t":              // TAB
                        return "\\t";
                    case "\u001A":          // Ctrl-Z
                        return "\\Z";
                    default:
                        return "\\" + v;
                }
            });
    }
}