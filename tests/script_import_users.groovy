import java.io.File; 
import org.cyclos.model.system.imports.UserImportedFileDTO; 
import org.cyclos.server.utils.SerializableInputStream ;
import org.cyclos.model.users.groups.BasicGroupQuery ;

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


