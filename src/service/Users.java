package service;

import helper.HibernateUtil;


import java.lang.reflect.InvocationTargetException;
import java.util.List;

import javax.persistence.EntityManager;
import javax.ws.rs.Consumes;
import javax.ws.rs.DELETE;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.PUT;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;

import org.apache.commons.beanutils.BeanUtils;

@Path("/users")
public class Users {
	
	@GET
	@Path("/{id}")
	@Produces({MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON})
	public List<Users> getUsers(@PathParam("id") Long seriesId) {
		List<Users> Users = HibernateUtil.getResults(
				"select u from Users u where u.seriesId = ?", new Object[] {seriesId} );
		return Users;
	}
	
	@PUT
	@Path("/{id}")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String updateUsers(Users u, @PathParam("id") Long id) {
		EntityManager em = HibernateUtil.getEntityManagerFactory().createEntityManager();
		Users existing = em.find(Users.class, id);
		if (existing == null) {
			System.err.println("Unable to find Users with ID = " + id);
			return "{success: false, message: 'Unable to find Users', data: []}";
		}
		
		em.getTransaction().begin();

		try {
			BeanUtils.copyProperties(existing, u);
		} catch (IllegalAccessException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (InvocationTargetException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		em.persist(existing);
		em.getTransaction().commit();
		return "{success: true, message: 'Updated', data: []}";
	}
	
	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String addUsers(Users u) {
		EntityManager em = HibernateUtil.getEntityManagerFactory()
				.createEntityManager();
		em.getTransaction().begin();
		em.persist(u);
		em.getTransaction().commit();
		return "{success: true, message: 'Added', data: []}";
	}
	
	@DELETE
	@Path("/{id}")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String deleteUsers(@PathParam("id") Long id) {
		
		int result = HibernateUtil.execute("delete from Users u where u.id = ?", 
				new Object[] {id});
		System.out.println("RESULT = " + result);
		return "{success: true, message: 'Deleted', data: []}";
	}
}
