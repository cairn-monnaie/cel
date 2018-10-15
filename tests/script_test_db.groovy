import java.io.File; 
import org.cyclos.model.system.imports.UserImportedFileDTO; 
import org.cyclos.model.system.imports.PaymentImportedFileDTO 
import org.cyclos.server.utils.SerializableInputStream ;
import org.cyclos.model.users.groups.BasicGroupQuery ;
import org.cyclos.model.system.imports.ImportedLineQuery ;
import org.cyclos.model.system.imports.ImportedLineStatus;
import java.util.Iterator;
import org.cyclos.model.system.imports.ImportedLineVO;
import org.cyclos.model.system.imports.ImportedFileQuery;
import org.cyclos.model.system.imports.ImportedFileStatus;
import org.cyclos.model.system.imports.ImportType;

//import members
File f_members = new File('./test_members.csv'); 
SerializableInputStream stream_members = new SerializableInputStream(f_members);

UserImportedFileDTO DTO_members = new UserImportedFileDTO() ;
def query_members = new BasicGroupQuery() ;
query_members.setName("pros") ;
res_members = groupService.search(query_members).getPageItems(); 
DTO_members.setGroup(res_members[0]) ;
DTO_members.setProcessAutomatically(true);
DTO_members.setFileName('./test_members.csv'); 

importService.upload(DTO_members,stream_members);

//import admins : does not work
//for now(cyclos 4.10.3) we can only import users in MEMBER_GROUP nature groups
//File f_admins = new File('./test_admins.csv'); 
//SerializableInputStream stream_admins = new SerializableInputStream(f_admins);
//
//UserImportedFileDTO DTO_admins = new UserImportedFileDTO() ;
//def query_admins = new BasicGroupQuery() ;
//query_admins.setName("networkAdmins") ;
//res_admins = groupService.search(query_admins).getPageItems(); 
//DTO_admins.setGroup(res_admins[0]) ;
//DTO_admins.setProcessAutomatically(true);
//DTO_admins.setFileName('./test_admins.csv'); 
//
//importService.upload(DTO_admins,stream_admins);

//import simple payments
//File file_payments = new File('./test_simple_payments.csv'); 
//SerializableInputStream stream_payments = new SerializableInputStream(file_payments);
//
//PaymentImportedFileDTO DTO_payments = new PaymentImportedFileDTO() ;
//
//DTO_payments.setFileName('./test_simple_payments.csv'); 
//importService.upload(DTO_payments, stream_payments) ;
